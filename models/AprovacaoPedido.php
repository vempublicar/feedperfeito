<?php

require_once __DIR__ . '/BaseModel.php';

class AprovacaoPedido extends BaseModel
{
    protected $table = 'aprovacoes_pedidos';

    protected $fillable = [
        'uid_usuario_pedido',
        'unique_code',
        'bonus',
        'imagens',
        'aprovacao',
        'num_revisao',
        'conversa',
        'data_revisao',
        'data_aprovacao',
        'data_entrega',
        'status_botao_download',
        'pedido_id', // Adicionado para associar a aprovação ao pedido
        'arquivos'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function createAprovacao($data)
    {
        return $this->create($data);
    }

    public function getAprovacaoByid($pedidoId)
    {
        $result = $this->where(['pedido_id' => $pedidoId]);
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return null;
    }

    public function updateAprovacao($id, $data)
    {
        return $this->update($id, $data);
    }
    /**
     * Atualiza a coluna 'conversa' e incrementa 'num_revisao' para uma aprovação de pedido específica.
     *
     * @param string $id O ID da aprovação do pedido.
     * @param array $novaMensagem A nova mensagem a ser adicionada ao array de conversa.
     * @return bool True se a atualização for bem-sucedida, false caso contrário.
     */
    public function updateConversa($id, $novaMensagem)
    {
        $aprovacao = $this->where(['id' => $id]);
        if (empty($aprovacao)) {
            return false;
        }
        $aprovacao = $aprovacao[0];

        $conversaAtual = $aprovacao['conversa'];
        if (is_string($conversaAtual)) {
            $conversaAtual = json_decode($conversaAtual, true);
        }
        if (!is_array($conversaAtual)) {
            $conversaAtual = [];
        }
        $conversaAtual[] = $novaMensagem;

        $dataToUpdate = [
            'conversa' => json_encode($conversaAtual),
            'num_revisao' => $aprovacao['num_revisao'] + 1,
        ];

        return $this->update($id, $dataToUpdate);
    }
}