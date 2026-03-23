<?php

namespace App\Livewire;

use App\Ai\Agents\DevSenior;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Objects\Image as AiImage;
use Laravel\Ai\Enums\Lab;

class ChatBot extends Component
{
    use WithFileUploads;

    public $messages = [];
    public $prompt = '';
    public $image;
    public $userId;  // int
    public $user;    // objetUser
    public $totalCost = 0;
    public $thisMsgCost = 0;
    private $agent;

    private array $prices = [
        'gemini-1.5-flash' => ['input' => 0.0004, 'output' => 0.0015],
    ];

    public function mount()
    {
        if (! auth()->check()) {
            auth()->loginUsingId(1);
        }

        $this->user   = Auth::user();
        $this->userId = $this->user->getKey();

        // Garante que o agente é sempre o mesmo instanceof DevSenior
        $this->agent = new DevSenior($this->user);

        if (! $this->agent) {
            throw new \RuntimeException('DevSenior agent failed to initialize');
        }
    }

    public function sendPrompt()
    {
        $key = 'ai-chat:' . $this->userId;

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('prompt', 'Limite de 10/min atingido.');
            return;
        }

        RateLimiter::hit($key, 60);

        if (strlen($this->prompt) > 2000 || empty(trim($this->prompt))) {
            return;
        }

        $this->messages[] = [
            'role'   => 'user',
            'content' => $this->prompt,
            'image'  => $this->image?->temporaryUrl() ?? null,
        ];

        $question = $this->prompt;
        $this->prompt = '';

        $this->dispatch('ask-ai', question: $question);
    }

    public function updatedImage()
    {
        $this->validate(['image' => 'image|max:5120']);
    }

    public function removeImage()
    {
        $this->image = null;
    }

    #[On('ask-ai')]
    public function ask($question)
    {
        if (! $this->agent) {
            $this->addError('prompt', 'O agente de IA não está inicializado.');
            \Log::error('DevSenior agent is null when trying to ask', [
                'user_id' => $this->userId,
            ]);
            return;
        }

        $this->messages[] = [
            'role'    => 'assistant',
            'content' => '',
            'image'   => null,
        ];

        $lastIndex = count($this->messages) - 1;

        $contents = [
            ['type' => 'text', 'text' => $question],
        ];

        if ($this->image) {
            $path = $this->image->store('ai', 'local');

            $contents[] = AiImage::fromPath(Storage::path($path))->detail('high');
            $this->image = null;
        }

        // Usando exatamente o fluxo oficial:
        // 1. `continue($conversationId, as: $user)`
        // 2. `streamContents(..., provider: ...)`
        $conversationId = session('conversation_id');

        $response = $this->agent
            ->forUser($this->user)                    // memória por usuário
            ->continue($conversationId, as: $this->user)
            ->streamContents($contents, provider: Lab::Gemini)
            ->then(function ($response) use ($lastIndex) {
                $usage  = $response->usage;
                $model  = $response->model ?? 'gemini-1.5-flash';
                $price  = $this->prices[$model] ?? ['input' => 0.0004, 'output' => 0.0015];

                $cost = (
                    $usage->promptTokens * $price['input'] +
                    $usage->completionTokens * $price['output']
                ) / 1000000;

                $this->thisMsgCost = round($cost, 4);
                $this->totalCost += $this->thisMsgCost;

                $this->messages[$lastIndex]['content'] .= $response->content;
            });

        session(['conversation_id' => $response->conversationId()]);
    }

    public function render()
    {
        return view('livewire.chat-bot');
    }
}
