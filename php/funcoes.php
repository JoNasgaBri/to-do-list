<?php
/**
 * Funções utilitárias para o sistema To-Do List
 * Incluir este arquivo em qualquer página que precise dessas funções
 */

/**
 * Formatar datas no padrão brasileiro
 * @param string $data - Data no formato MySQL (Y-m-d ou Y-m-d H:i:s)
 * @param bool $incluirHora - Se deve incluir hora na formatação
 * @return string - Data formatada no padrão brasileiro
 */
function formatarDataBrasil($data, $incluirHora = false) {
    if (empty($data) || $data == '0000-00-00' || $data == '0000-00-00 00:00:00') {
        return 'Não definida';
    }
    
    try {
        $timestamp = strtotime($data);
        if ($timestamp === false) {
            return 'Data inválida';
        }
        
        if ($incluirHora) {
            return date("d/m/Y H:i", $timestamp);
        } else {
            return date("d/m/Y", $timestamp);
        }
    } catch (Exception $e) {
        return 'Data inválida';
    }
}

/**
 * Converter data do formato brasileiro (dd/mm/aaaa) para formato MySQL (aaaa-mm-dd)
 * @param string $data - Data no formato brasileiro
 * @return string - Data no formato MySQL ou string vazia se inválida
 */
function converterDataParaMySQL($data) {
    if (empty($data)) {
        return '';
    }
    
    // Remover caracteres não numéricos exceto /
    $data = preg_replace('/[^0-9\/]/', '', $data);
    
    // Verificar se está no formato dd/mm/aaaa
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $data, $matches)) {
        $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $mes = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $ano = $matches[3];
        
        // Validar se a data é válida
        if (checkdate($mes, $dia, $ano)) {
            return "$ano-$mes-$dia";
        }
    }
    
    return '';
}

/**
 * Calcular diferença de dias entre duas datas
 * @param string $dataInicio - Data inicial
 * @param string $dataFim - Data final (opcional, padrão é hoje)
 * @return int - Diferença em dias
 */
function calcularDiferencaDias($dataInicio, $dataFim = null) {
    if ($dataFim === null) {
        $dataFim = date('Y-m-d');
    }
    
    try {
        $inicio = new DateTime($dataInicio);
        $fim = new DateTime($dataFim);
        $diferenca = $inicio->diff($fim);
        
        return $diferenca->days * ($diferenca->invert ? -1 : 1);
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Formatar status de tarefa com cores
 * @param string $status - Status da tarefa
 * @return string - HTML com classe CSS para colorir
 */
function formatarStatusTarefa($status) {
    $classes = [
        'Pendente' => 'status-pendente',
        'Em Andamento' => 'status-andamento',
        'Concluída' => 'status-concluida'
    ];
    
    $classe = isset($classes[$status]) ? $classes[$status] : 'status-padrao';
    
    return "<span class='$classe'>" . htmlspecialchars($status) . "</span>";
}

/**
 * Verificar se uma data limite está próxima (próximos 3 dias)
 * @param string $dataLimite - Data limite no formato MySQL
 * @return bool - True se está próxima do vencimento
 */
function dataProximaVencimento($dataLimite) {
    if (empty($dataLimite)) {
        return false;
    }
    
    $dias = calcularDiferencaDias(date('Y-m-d'), $dataLimite);
    return $dias >= 0 && $dias <= 3;
}

/**
 * Verificar se uma data limite já passou
 * @param string $dataLimite - Data limite no formato MySQL
 * @return bool - True se já passou
 */
function dataVencida($dataLimite) {
    if (empty($dataLimite)) {
        return false;
    }
    
    return calcularDiferencaDias(date('Y-m-d'), $dataLimite) < 0;
}
?>
