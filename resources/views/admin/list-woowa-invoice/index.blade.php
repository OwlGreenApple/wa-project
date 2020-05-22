@extends('layouts.admin')

@section('content')
<script type="text/javascript">
  var table;

  $(document).ready(function() {
    table = $('#myTable').DataTable({
                responsive : true,
                destroy: true,
                "order": [],
            });
    $.fn.dataTable.moment( 'ddd, DD MMM YYYY' );

    refresh_page();

    // $('.formatted-date').datepicker({
    //   dateFormat: 'yy/mm/dd',
    // });
  });

  function refresh_page(){
    table.destroy();
    $.ajax({
      type : 'GET',
      url : "<?php echo url('/list-invoice/load') ?>",
      dataType: 'text',
    //   beforeSend: function()
    //   {
    //     $('#loader').show();
    //     $('.div-loading').addClass('background-load');
    //   },
      success: function(result) {
        // $('#loader').hide();
        // $('.div-loading').removeClass('background-load');

        var data = jQuery.parseJSON(result);
        $('#content').html(data.view);
        
        table = $('#myTable').DataTable({
                  responsive : true,
                  destroy: true,
                  "order": [],
                });

      }
    });
  }

 </script>

<section id="tabs" class="col-md-10 offset-md-1 col-12 pl-0 pr-0 project-tab" style="margin-top:30px;margin-bottom: 120px;">
  <div class="container body-content-mobile main-cont">
    <div class="row">
    <div class="col-md-11">

      <h2><b>WooWA</b></h2>  
      
      <h5>
        Show you previous history orders
      </h5>
      
      <hr>

      <div id="pesan" class="alert"></div>

      <br>  

      <form>
        <table class="table" id="myTable">
          <thead align="center">
            <th data-priority="1" action="no_order all">
              No Invoice
            </th>
            <th action="grand_total all">
              Total
            </th>
            <th action="created_at none">
              Created
            </th>
            <th class="all">
              Status
            </th>
          </thead>
          <tbody id="content"></tbody>
        </table>

        <div id="pager"></div>    
      </form>
    </div>
  </div>
</div>


</section>

<script type="text/javascript">
  $( "body" ).on( "click", ".btn-search", function() {
    currentPage = '';
    refresh_page();
  });


  $(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    currentPage = $(this).attr('href');
    refresh_page();
  });
</script>
@endsection