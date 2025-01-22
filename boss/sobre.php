<? include("../connect.php"); include("../includes/Funcoes/sessao.php"); include("../includes/Funcoes/Funcoes.php");
$file = explode("/", $_SERVER['PHP_SELF']); $file = end($file);


//Verifica se o usuário tem acesso à essa área
$area = 'Sobre'; include("../includes/Funcoes/acessoArea.php");

$tabela = 'sobre';
$novoTamanho = array( 438, 266, '../../conteudo/sobre/', '' , '', '', '');

/* Seleciona todos os produtos */
$sqlt = CRUD::SELECT('', $tabela, '', '', 'LIMIT 1');
$total_reg = sizeof($sqlt);

/* Seleciona dados do usuário selecionado */
$id = isset($_GET['id'])?intval($_GET['id']):0;
$a = CRUD::SELECT_ID('', $tabela, $id);

$id = isset($_GET['id']) ? $_GET['id'] : "";
$nomee = isset($_GET['nomee']) ? $_GET['nomee'] : "";

$inserir   = isset($_POST['inserir'])   ? $_POST['inserir']   : "";
$titulo    = isset($_POST['titulo'])    ? $_POST['titulo']    : "";
$link      = isset($_POST['link'])    ? $_POST['link']    : "";
$nome_botao      = isset($_POST['nome_botao'])    ? $_POST['nome_botao']    : "";
$texto     = isset($_POST['texto'])    ? $_POST['texto']    : "";


if ($inserir=='true') {
    $params = array('titulo'=>$titulo,  'texto'=>$texto, 'link'=>$link, 'nome_botao'=> $nome_botao);

    if($acao=='editar'){
        if(CRUD::UPDATE($tabela, $params,$id)) print "<script>window.location='$file?alert=Portfolio alterado&tp-alert=success';</script>";
        TIMELINE::add($idU, $nomeU, $area, 'Editou o Sobre');
    } else {
        if($id = CRUD::INSERT_ID($tabela, $params)) print "<script>window.location='$file?acao=editar&id=$id&alert=Portfolio adcionado<br>Adcione as Imagens&tp-alert=success';</script>";
        TIMELINE::add($idU, $nomeU, $area, 'Inseriu o Sobre');
    }
}

if($acao=='excluir'){
    if(CRUD::DELETE($tabela, $id)) print "<script>window.location='$file?alert=Portfolio deletado&tp-alert=success';</script>";
    TIMELINE::add($idU, $nomeU, $area, 'Excluiu o Sobre');

} else if($acao=='excluirimg'){
    $img = isset($_GET['img'])?$_GET['img']:'';
    if(CRUD::DELETE('sobre_imagens', $img)) print "<script>window.location='$file?acao=editar&id=$id';</script>";
}
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BOSS v.3</title>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>css/sb-admin.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>css/bootstrap-switch.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.css" rel="stylesheet"/>
    <link href="<?=BOSS_TATICA?>js/SimpleNotifications/style.css" rel="stylesheet"/>
    <link href="<?=BOSS_TATICA?>css/bootstrap-switch.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.css" rel="stylesheet"/>
    <link href="<?=BOSS_TATICA?>css/estilo.css" rel="stylesheet">

    <script src="<?=BOSS_TATICA?>js/jquery-1.10.2.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="<?=BOSS_TATICA?>js/sb-admin.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="<?=BOSS_TATICA?>js/bootstrap-switch.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.js"></script>
    <script src="<?=BOSS_TATICA?>js/bootstrap-maxlength.js"></script>
    <script src="<?=BOSS_TATICA?>js/jquery.maskedinput.js"></script>
    <script src="<?=BOSS_TATICA?>js/tinymce/js/tinymce/tinymce.min.js"></script>
    <script src="<?=BOSS_TATICA?>js/SimpleNotifications/ttw-simple-notifications.js"></script>
    <script src="<?=BOSS_TATICA?>js/bootstrap-switch.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/date-br.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/datetime-br.js"></script>
    <script src="<?=BOSS_TATICA?>js/jquery-ui-sortable.js"></script>
    <style type="text/css">
        a{text-decoration: none !important;}
    </style>
</head>
<body>

    <script type="text/javascript">

        $(document).ready(function () {

            var notifications = $('body').ttwSimpleNotifications();
            <?=isset($_GET['alert'])?'notifications.show("'.$_GET['alert'].'");':'';?>
            <?=isset($_GET['tp-alert'])?'$(".ttw-simple-notification").addClass("'.$_GET['tp-alert'].'");':'';?>

            <?=($acao!='editar' && $acao!='novo')?"$('#edit').hide();":""?>

            $('#dataTables').dataTable({
                "aaSorting": [[ 1, "desc" ]], //ordenação padrão da tabela por ordem crescente(asc) da primeira coluna(0)
                "aoColumns": [
                    { "sType": "html" }, //opção de ordenar por data
                    null
                ]
            });

            //Abre uma imagem em zomm no lightbox e envia as informações dela
            $('.abreModal').click(function(){
                var id=$(this).attr("data-id");
                var src=$(this).attr("data-src");
                $('#modalFullscreenImagem .modal-body img').attr('src','<?=$novoTamanho[2]?>'+src);
                $('#modalFullscreenImagem .modal-footer a.capa').attr('href','?acao=imgcapa&id=<?=$id?>&img='+id);
                $('#modalFullscreenImagem .modal-footer a.excluir').attr('href','?acao=excluirimg&id=<?=$id?>&img='+id+'&imgnome='+src);
                if ($('#capa').val() == id){
                    $('#modalFullscreenImagem .modal-footer a.capa .btn').html('Esta imagem é a capa').addClass('btn-success').removeClass('btn-default');
                }else{
                    $('#modalFullscreenImagem .modal-footer a.capa .btn').html('Definir como capa').addClass('btn-default').removeClass('btn-success');;
                }
                $('#modalFullscreenImagem').modal('show');
            });

            //Abre um lightbox para fazer o upload
            $('.upload').click(function(){
                $('#modalUploadImagem').modal('show');
            });

            $('li').hover(function(){$(this).tooltip('show')});
            $(function() {
                $("#loadImagens ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
                    //especifica arquivo p/ atualizar no Banco e informa a tabela
                    var order = $(this).sortable("serialize") + '&action=updateRecordsListings&tabela=<?=$tabela?>_imagens';
                     $.post("<?=SITE_URL?>boss/includes/Funcoes/updateOrdemDB.php", order, function(theResponse){
                        //atualiza os números só p/ exibição(BD já foi alterado)
                        var ordem = 1;
                        $('#loadImagens ul li:first-child').each(function(){
                            //$(this).html(ordem); ordem = ordem +1;
                        });
                    });
                }
                });
            });

            $("[name='bloqueado']").bootstrapSwitch();


            //Faz upload da imagem
            jQuery('#formImagem').submit(function(){
                var erros=0;
                if (document.formImagem.imagem.value == ""){ $('#frameImagem').addClass("error"); $('#imagem').next('span').html('<h6 class="msgerro">Selecione a imagem</h6>'); erros=1; }
                if (erros==0){ document.formImagem.inserirIMG.value = 'true';

                    //envia os dados por ajax
                    var dados = jQuery( this ).serialize();
                    jQuery.ajax({
                        type: "POST",
                        url: "add_imagens.php",
                        data: dados,
                        success: function( data )
                        {
                            //atualiza lista de miniaturas de imagens
                            $('#loadImagens').html(data);
                            //zera o upload
                            $('#frameImagem').attr('src','upload.php?w=<?=$novoTamanho[0];?>&h=<?=$novoTamanho[1];?>');
                            //fecha lightbox
                            $('#modalUploadImagem').modal('hide');
                        }
                    });
                }
                return false;
            });
        });

        function verificacao(){
            $('.msgerro').remove();
            $('.error').removeClass('error');

            var erros=0;
            if (document.form.titulo.value == ""){ $('#titulo').addClass("error"); $('#titulo').next('span').html('<h6 class="msgerro">Informe o titulo</h6>'); erros=1; }
            if (erros==0){ document.form.inserir.value = 'true'; document.form.submit(); }
        }

        function confirmacao(id){
            var resposta = confirm("Deseja remover esse registro?");
            if (resposta == true) {
                  window.location.href = "?acao=excluir&id="+id;
            }
        }

    </script>

    <? include('../topo.php');include('../menu.php');?>

    <style>
.form-horizontal .form-group{margin-right: 0px; margin-left: 0px;}
    </style>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Sobre</h1>
            </div>
        </div>

        <div class="row" id="edit">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="<?=$acao==''?'glyphicon glyphicon-plus-sign':'glyphicon glyphicon-pencil'?>"></i> <?=$acao=='editar'?'Editar':'Novo'?> Sobre
                    </div>
                    <div class="panel-body">
                        <? if($acao=='editar' && empty($a)){ ?>
                            <div class="col-lg-12">
                                <h4>Sobre não encontrado!</h4>
                                <hr>
                            </div>
                        <? } else { ?>
                        <form role="form" name="form" action="" method="post" class="form-horizontal">
                            <fieldset>
                                <div class="col-md-6">
                                    <div class="form-group col-lg-12">
                                        <label for="titulo" class="control-label">Titulo</label>
                                        <input class="form-control" name="titulo" id="titulo" type="text" value="<?=isset($a['titulo'])?$a['titulo']:''?>"><span></span>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label for="titulo" class="control-label">Link:</label>
                                        <input class="form-control" name="link" id="link" type="text" value="<?=isset($a['link'])?$a['link']:''?>"><span></span>
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label for="titulo" class="control-label">titulo do Botao:</label>
                                        <input class="form-control" name="nome_botao" id="nome_botao" type="text" value="<?=isset($a['nome_botao'])?$a['nome_botao']:''?>"><span></span>
                                    </div>


                                    <div class="form-group col-lg-12">
                                        <label for="titulo" class="control-label">Texto:</label>
                                        <textarea class="form-control" name="texto" id="texto"><?=isset($a['texto'])?$a['texto']:''?></textarea><span></span>
                                    </div>



                                </div>
                                <div class="col-md-6">

                                    <div class="form-group col-lg-12">
                                        <label class="control-label">Imagens:</label><br>

                                        <? if($acao=='editar'){ ?>
                                        <div id="loadImagens">
                                            <? $capa="";
                                            if ($id!=0){
                                            $sqlImg = CRUD::SELECT('', 'sobre_imagens', 'idPai=:id', array('id'=>$id), 'order by ordem');
                                            echo '<ul id="">';
                                            foreach ($sqlImg as $key => $i) {?>
                                                <li style="float:left; margin:5px; list-style:none; padding-left:0;" id="recordsArray_<?=$i['id']?>" a data-toggle="tooltip" data-placement="top" data-html="true" data-animation="true" title="<i class='fa fa-exchange fa-rotate-90'></i> Arraste para ordenar">
                                                    <div class="thumbnail" style="float:left; margin:5px;">
                                                        <img src="<?=$novoTamanho[2].$i['imagem']?>" class="img-responsive" width="120px" style="margin-bottom:5px;">

                                                        <a href="?acao=excluirimg&id=<?=$id?>&img=<?=$i['id']?>&imgnome=<?=$i['imagem']?>">
                                                            <div class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></div>
                                                        </a>
                                                        <a class="abreModal" data-id="<?=$i['id']?>" data-src="<?=$i['imagem']?>">
                                                            <div class="btn btn-info btn-xs"><i class="glyphicon glyphicon-fullscreen"></i></div>
                                                        </a>
                                                    </div>
                                                </li>
                                             <? }
                                             echo '</ul>';
                                            }?>
                                            <input type="hidden" name="capa" value="<?=$capa?>" id="capa">
                                        </div>

                                        <div class="thumbnail upload" style="float:left; margin:5px;">
                                            <div style="width:120px; height:110px; display:block; background: #eee;text-align: center;font-size: 30px;padding-top: 33%; cursor: pointer;">
                                                <i class="glyphicon glyphicon-plus"></i>
                                            </div>
                                        </div>
                                        <? } else { ?> <p>Clique em OK para salvar o produto antes de adcionar as imagens.</p> <? } ?>

                                            <!-- Modal Fullscreen Imagem -->
                                            <div class="modal fade" id="modalFullscreenImagem" tabindex="-1" role="dialog" aria-labelledby="labelFullscreenImagem" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                    <div class="modal-body">
                                                        <img src="" class="img-responsive" id="imagemFull" data-id="">
                                                    </div>
                                                    <div class="text-left modal-footer">
                                                        <a href="" class="capa">
                                                            <div class="btn btn-default">Definir como Capa</div>
                                                        </a>

                                                        <a href="" class="excluir">
                                                            <div class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> Excluir</div>
                                                        </a>

                                                        <div class="btn btn-default" data-dismiss="modal" aria-hidden="true" style="position: absolute; right:20px;"><i class="glyphicon glyphicon-remove"></i></div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br>
                                <div class="col-sm-2 col-sm-offset-10">
                                    <input type="hidden" name="inserir">
                                    <a class="btn btn-lg btn-success btn-block" onClick="verificacao()">OK</a>
                                </div>
                            </fieldset>
                        </form>
                        <? } ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Upload Imagem -->
        <div class="modal fade" id="modalUploadImagem" tabindex="-1" role="dialog" aria-labelledby="labelUploadImagem" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                <div class="modal-body">
                    <form role="form" name="formImagem" action="" method="post" class="form-horizontal" id="formImagem">
                            <iframe src="upload.php?w=<?=$novoTamanho[0];?>&h=<?=$novoTamanho[1];?>" id='frameImagem' name="frameImagem" frameborder="0" scrolling="no" style="width: 100%; height:60px;"></iframe>
                            <input type="hidden" name="x1" id="x1" value="" />
                            <input type="hidden" name="y1" id="y1" value="" />
                            <input type="hidden" name="h" id="h" value="" />
                            <input type="hidden" name="w" id="w" value="" />

                            <input name="imagem" type="hidden" id="imagem" />
                            <input name="idPai" type="hidden" value="<?=$id?>" />
                            <input name="tabela" type="hidden" value="<?=$tabela?>_imagens" />
                            <input name="tabela_img" type="hidden" value="<?=$tabela?>_imagens" />
                            <!-- $novoTamanho = array( 390, 390, '../../img/', '' ,0 , 0 , ''); os input abaixo são referentes ao array novoTamanho -->
                            <input type="hidden" name="nTwidth" value="<?=$novoTamanho[0]?>" />
                            <input type="hidden" name="nTheight" value="<?=$novoTamanho[1]?>" />
                            <input type="hidden" name="nTpasta" value="<?=$novoTamanho[2]?>" />
                            <input type="hidden" name="nTmascara" value="<?=$novoTamanho[3]?>" />
                            <input type="hidden" name="nTthumbwidth" value="<?=$novoTamanho[4]?>" />
                            <input type="hidden" name="nTthumbheight" value="<?=$novoTamanho[5]?>" />
                            <input type="hidden" name="nTthumbpasta" value="<?=$novoTamanho[6]?>" />

                            <span></span>

                            <input type="hidden" name="inserirIMG" value="" />
                      </div>
                      <div class="text-left modal-footer">
                        <input type="submit" class="btn btn-success" value="Enviar">
                    </form>

                    <div class="btn btn-default" data-dismiss="modal" aria-hidden="true" style="position: absolute; right:20px;"><i class="glyphicon glyphicon-remove"></i></div>
                </div>
                </div>
            </div>
        </div>

        <div class="row" id="list">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                       Editar ou Cadastrar novas Sobre:
                        <a <?=$acao!=''?'href="?acao=novo"':'class="add"'?> style="float:right;" onClick="$('#edit').slideDown();">
                            <div class="btn btn-success btn-xs"><i class="glyphicon glyphicon-plus-sign"></i> Novo</div>
                        </a>
                    </div>
                    <div class="panel-body">
                        <? if ($total_reg==0){ echo "Nenhum registro encontrado.";} else { ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="dataTables">
                                <thead>
                                    <tr class="drag-order">
                                        <th style="width: 90%">Título</th>
                                        <th style="width: 10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? $cont = 1;
                                    foreach ($sqlt as $key => $t) { ?>
                                        <tr class="<?=$cont%2==0?'odd':'even'?>" id="recordsArray_<?=$t['id']?>">
                                            <td><?=$t['titulo']?></td>
                                            <th>
                                                <a href="?acao=editar&id=<?=$t['id']?>&nomee=<?=$t['titulo']?>" title="Editar" name="Editar" style="margin-top:10px; float: left">
                                                    <div class="btn btn-info btn-xs"><i class="glyphicon glyphicon-pencil"></i></div>
                                                </a>
                                                <a onclick="confirmacao('<?=$t['id']?>')" title="Excluir" name="Excluir" style="float: left;margin: 10px;">
                                                    <div class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></div>
                                                </a>
                                            </th>
                                        </tr>
                                    <? }?>
                                </tbody>
                            </table>
                        </div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <? include("../rodape.php"); ?>

</body>

</html>
