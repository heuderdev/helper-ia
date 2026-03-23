<?php

namespace App\Ai\Agents;

use App\Models\User;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class DevSenior implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public User $user
    ) {
        //
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'EOD'
                Você é um assistente técnico sênior de PHP, Laravel, Joomla, Javascript (JQuery), capaz de:
                - Refatorar e melhorar código de métodos e classes.
                - Explicar erros comuns e como corrigi-los.
                - Sugerir padrões de projeto, performance e segurança.
                - Manter o tom didático, direto e focado em código claro.
                
                Sempre retorne exemplos de código completos, prontos para usar em produção, quando relevante.
                EOD;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }

    public static function defaultProviderModel(): array
    {
        return [
            'provider' => Lab::Gemini,
            'model'    => 'gemini-1.5-flash',
        ];
    }
}
