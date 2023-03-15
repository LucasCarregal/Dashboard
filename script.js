$(document).ready(() => {
	
    $('#dashboard').on('click', function (){
        $.post('dashboard.html', function (data){
            $('#pagina').html(data)
        })
    })

    $('#documentacao').on('click', function (){
        $.post('documentacao.html', function (data){
            $('#pagina').html(data)
        })
    })

    $('#suporte').on('click', function (){
        $.post('suporte.html', function (data){
            $('#pagina').html(data)
        })
    })

    $('#compentencia').on('change', function (e){
        let competencia = $(e.target).val()

        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,
            dataType: 'json',
            success: function (dados){
                $('#numero_vendas').html(dados.numeroVendas)
                $('#total_vendas').html('R$ ' + dados.totalVendas)

                $('#clientes_ativos').html(dados.clientesAtivos)
                $('#clientes_inativos').html(dados.clientesInativos)

                $('#reclamacoes').html(dados.totalReclamacoes)
                $('#elogios').html(dados.totalElogios)
                $('#sugestoes').html(dados.totalSugestoes)

                $('#total_despesas').html('R$ ' + dados.totalDespesas)
                
            },
            error: function (erro){console.log('Vish, ocorreu algum erro!')}
        })
    })

})