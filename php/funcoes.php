<?php
/**
 * Funcoes utilitarias para o sistema To-Do List
 */

/**
 * Formatar datas no padrao brasileiro
 */
function formatarDataBrasil($data, $incluirHora = false) {
    if (empty($data) || $data == '0000-00-00' || $data == '0000-00-00 00:00:00') {
        return 'Nao definida';
    }
    
    $timestamp = strtotime($data);
    if ($timestamp === false) {
        return 'Data invalida';
    }
    
    if ($incluirHora) {
        return date("d/m/Y H:i", $timestamp);
    } else {
        return date("d/m/Y", $timestamp);
    }
}

/**
 * Converter data do formato brasileiro para formato MySQL
 */
function converterDataParaMySQL($data) {
    if (empty($data)) {
        return '';
    }
    
    $data = preg_replace('/[^0-9\/]/', '', $data);
    
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $data, $matches)) {
        $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $mes = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $ano = $matches[3];
        
        if (checkdate($mes, $dia, $ano)) {
            return "$ano-$mes-$dia";
        }
    }
    
    return '';
}

/**
 * Calcular diferenca de dias entre duas datas
 */
function calcularDiferencaDias($dataInicio, $dataFim = null) {
    if ($dataFim === null) {
        $dataFim = date('Y-m-d');
    }
    
    $inicio = new DateTime($dataInicio);
    $fim = new DateTime($dataFim);
    $diferenca = $inicio->diff($fim);
    
    return $diferenca->days * ($diferenca->invert ? -1 : 1);
}

/**
 * Formatar status de tarefa com cores
 */
function formatarStatusTarefa($status) {
    $classes = array(
        'Pendente' => 'status-pendente',
        'Em Andamento' => 'status-andamento',
        'Concluida' => 'status-concluida'
    );
    
    $classe = isset($classes[$status]) ? $classes[$status] : 'status-padrao';
    
    return "<span class='" . $classe . "'>" . htmlspecialchars($status) . "</span>";
}

/**
 * Verificar se uma data limite esta proxima (proximos 3 dias)
 */
function dataProximaVencimento($dataLimite) {
    if (empty($dataLimite)) {
        return false;
    }
    
    $dias = calcularDiferencaDias(date('Y-m-d'), $dataLimite);
    return $dias >= 0 && $dias <= 3;
}

/**
 * Verificar se uma data limite ja passou
 */
function dataVencida($dataLimite) {
    if (empty($dataLimite)) {
        return false;
    }
    
    return calcularDiferencaDias(date('Y-m-d'), $dataLimite) < 0;
}
?>
