
<?php get_header() ?>

<section class="space-vertical">
  <div class="row">
    <div class="col span-2-7">
    
      <?php echo do_shortcode('[contact-form-7 id="4" title="Estado"]');?>

      <div id="selectcidade"">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <div class="col span-2-7" >     
    </div>
  </div>
  
</section>

<script>

  $("#cidade").attr("disabled","disabled");    

  var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>'; //ajax do wordpress

  $('body').on('change', '#estado', function (){

    $('#cidade').find('option').remove().end();
        
    var estado = $(this).val();

    if(estado != '') {
      var data = {

        action: 'get_estados_cf7_by_ajax',
        estado: estado
      }

      $.post(ajaxurl, data, function(response) {
        
      response = JSON.parse(response);

      for(var i = 0; i < response.values.length; i++){
        $('<option value="'+ response.values[i] +'">' 
        + response.labels[i] + "</option>").appendTo($('#cidade'));
      }

      $("#cidade").removeAttr("disabled");
      });
    }
  });
</script>

<?php get_footer() ?>
