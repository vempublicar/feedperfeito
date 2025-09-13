<?php
require_once __DIR__ . '/../config/database.php';

class CreditManager
{
    public function __construct()
    {
        // A conexão com o banco de dados Supabase é gerenciada pelas funções globais.
    }

    public function addCredits(string $userId, float $amount, string $description = 'Adição de créditos', string $type = 'credit')
    {
        if ($amount <= 0) {
            return false; // Retorna false em caso de valor inválido
        }

        try {
            // Obtém o saldo atual
            $currentCredits = $this->getCredits($userId);
            $newCredits = $currentCredits + $amount;

            // Atualiza o saldo na tabela 'profile'
            $profileUpdateData = ['credits' => $newCredits];
            $response = supabase_request('profiles?id=eq.' . urlencode($userId), 'PATCH', $profileUpdateData);

            if (empty($response)) {
                throw new Exception("Falha ao atualizar créditos do perfil.");
            }

            // Registra a transação na tabela de extrato
            $transactionData = [
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $type, // Usar o tipo passado como parâmetro
                'description' => $description
            ];
            $response = supabase_request('credit_transactions', 'POST', $transactionData);

            if (empty($response)) {
                throw new Exception("Falha ao registrar transação de crédito.");
            }

            return $newCredits; // Retorna o novo saldo
        } catch (Exception $e) {
            error_log("Erro ao adicionar créditos: " . $e->getMessage());
            return false; // Retorna false em caso de erro
        }
    }

    public function removeCredits(string $userId, float $amount, string $description = 'Retirada de créditos')
    {
        if ($amount <= 0) {
            return false; // Retorna false em caso de valor inválido
        }

        try {
            // Verifica se o usuário tem créditos suficientes
            $currentCredits = $this->getCredits($userId);
            if ($currentCredits < $amount) {
                return false; // Retorna false se o saldo for insuficiente
            }

            // Atualiza o saldo na tabela 'profile'
            $newCredits = $currentCredits - $amount;
            $profileUpdateData = ['credits' => $newCredits];
            $response = supabase_request('profiles?id=eq.' . urlencode($userId), 'PATCH', $profileUpdateData);

            if (empty($response)) {
                throw new Exception("Falha ao atualizar créditos do perfil na remoção.");
            }

            // Registra a transação na tabela de extrato
            $transactionData = [
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description
            ];
            $response = supabase_request('credit_transactions', 'POST', $transactionData);

            if (empty($response)) {
                throw new Exception("Falha ao registrar transação de débito.");
            }

            return $newCredits; // Retorna o novo saldo
        } catch (Exception $e) {
            error_log("Erro ao remover créditos: " . $e->getMessage());
            return false;
        }
    }

    public function refundCredits(string $userId, float $amount, string $description = 'Estorno de créditos')
    {
        return $this->addCredits($userId, $amount, $description, 'refund'); // Passa o tipo 'refund'
    }

    public function bonusCredits(string $userId, float $amount, string $description = 'Créditos de bônus')
    {
        return $this->addCredits($userId, $amount, $description, 'bonus'); // Passa o tipo 'bonus'
    }

    public function getCredits(string $userId): float
    {
        try {
            $response = supabase_request('profiles?id=eq.' . urlencode($userId) . '&select=credits');
            if (!empty($response) && isset($response[0]['credits'])) {
                return (float)$response[0]['credits'];
            }
            return 0.0;
        } catch (Exception $e) {
            error_log("Erro ao obter créditos: " . $e->getMessage());
            return 0.0;
        }
    }
}