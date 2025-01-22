
    <div class="nav nav-fixed-bottom rodape">
        <p class="text-center">BOSS v.3 - Painel Administrativo por Tática Web<a href="http://www.taticaweb.com.br"><img src="<?=SITE_URL?>boss/img/tatica_web.png" align="right"></a></p>
        
    </div>

<!-- Ajusta Altura da Ppágina -->
<script type="text/javascript">

$(document).ready(function() {

	$('#formsexo').on('change', function() {
		$('#submit').click();
	});

	if($('#page-wrapper').length){
		var heightTotal = window.innerHeight;
		heightTotal = heightTotal - 72 - 46;
		var heightPage = $('#page-wrapper').height();
		if(heightTotal>heightPage){
			$('#page-wrapper').css('min-height',heightTotal);
		}
	} else if($('.container').length){
		var heightTotal = window.innerHeight;
		heightTotal = heightTotal - 46;
		var heightPage = $('.container').height();
		if(heightTotal>heightPage){
			$('.container').css('min-height',heightTotal);
		}
	}
});
</script>

    