@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>LISTS</h2>
  </div>

  <div class="act-tel-dashboard-right">
     <a href="{{url('list-form')}}" class="btn btn-custom">Create List</a>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container mt-2">
  <div class="act-tel-tab">
      <div class="input-group">
          <input type="text" name="listname" class="form-control-lg col-lg-4 search-box" placeholder="Find a List By a name" >
          <span class="input-group-append">
            <div class="btn search-icon">
                <span class="icon-search"></span>
            </div>
          </span>
      </div>
  </div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="act-tel-tab">
      <div class="col-lg-12" id="display_list">
        <!-- display data -->
      </div>
  </div>
</div>

<!-- Modal Copy Link -->
<div class="modal fade" id="copy-link" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Copy Link
        </h5>
      </div>
      <div class="modal-body">
        You have copied the link!
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-dismiss="modal">
          OK
        </button>
      </div>
    </div>
      
  </div>
</div>

<!-- Modal  -->
<div class="modal fade" id="table-data" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Data Subscriber
        </h5>
      </div>
      <div class="modal-body">
        <table id="datasubscriber" class="table table-responsive" style="width : 100%">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Phone</th>
              <th>Username</th>
              <!--<th>Email</th>-->
            </tr>
          </thead>
          <tbody id="data-customer"></tbody>
        </table> 
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function(){
    displayData();
    searchList();
    deleteList();
    duplicateList();
    copyLink();
    openTable();
  });

  function openTable(){
      $("body").on("click",".open-table",function(){
        var id = $(this).attr('id');
        $("#table-data").modal();
        $.ajax({
          type : 'GET',
          url : '{{ url("list-customer") }}',
          data : {list_id : id},
          dataType : 'html',
          success : function(result)
          {
            $("#data-customer").html(result);
          },
          error : function(xhr,attr,throwable)
          {
            alert(xhr.responseText);
          }
        });
      });
  }

  function table(){
      $("#datasubscriber").DataTable({
          'pageLength': 5,
          'processing': true,
          'serverSide': true,
          'serverMethod': 'get',
          'ajax': {
              'url':'{{ url("list-customer") }}'
          },
          'columns': [
             { data: 'name' },
             { data: 'phone' },
             { data: 'username' },
             { data: 'email' },
          ]
      });
  }

  function displayData(){
    $.ajax({
      type : 'GET',
      url : '{{url("lists-table")}}',
      dataType : 'html',
      success : function(result){
        $("#display_list").html(result);
      }
    });
  }

  function searchList(){
    $(".search-icon").click(function(){
        var listname = $("input[name=listname]").val();

        $.ajax({
          type : 'GET',
          url : '{{route("searchlist")}}',
          data : {'listname' : listname},
          dataType : 'html',
          success : function(result){
            $("#display_list").html(result);
          }
        });
    });
  }

   function deleteList(){
    $('body').on('click',".del",function(){
      var id = $(this).attr('id');
      var conf = confirm('Are you sure to delete this list?');

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : "{{route('deletelist')}}",
          data : {'id' : id},
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            alert(result.message);
            displayData();
          }
        });
      } else {
        return false;
      }
     
    });
  }

  function duplicateList(){
    $("body").on("click",".duplicate",function(){
        var id = $(this).attr('id');
        var conf = confirm('Are you want to duplicate this list?');
        
        if(conf == true)
        {
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
           $.ajax({
              type : 'POST',
              url : '{{route("duplicatelist")}}',
              data : {'id' : id},
              dataType : "json",
              beforeSend: function()
              {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
              },
              success : function(result)
              {
                 $('#loader').hide();
                 $('.div-loading').removeClass('background-load');

                 if(result.error == true)
                 {
                    alert(result.message);
                 }
                 else
                 {
                    alert(result.message);
                    displayData();
                 }
              }
          });
        }
        else {
          return false;
        }
        
    });
    }

    function copyLink(){
      $( "body" ).on("click",".btn-copy",function(e) 
      {
        e.preventDefault();
        e.stopPropagation();

        var link = $(this).attr("data-link");

        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $('#copy-link').modal('show');
      });
    }

    /*$(document).ready(function(){
        table();
        displayDataAdditional();
        openImport();
        csvImport();
    });

    //TO IMPORT
    function openImport()
    {
        $(".import-col").click(function(){
            $("#import_popup").modal();
            var id = $(this).attr('id');
            $("input[name='list_id_import']").val(id);
        });
    } 

    function csvImport()
    {
        $("body").on("submit","#import_list_subscriber",function(e){
            e.preventDefault();
            var form = $(this)[0];
            var data = new FormData(form);

            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
            $.ajax({
                type : 'POST',
                enctype: 'multipart/form-data',
                url : '{{url("import_csv_list_subscriber")}}',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                data : data,
                dataType : 'json',
                success : function(response)
                {
                    alert(response.message);
                }
            })
        });
    }

    function displayDataAdditional(){
        $("body").on("click",".addt",function(){
            var listid = $(this).attr('id');
            var boxdata = '';
            $("#additional_popup").modal();

            $.ajax({
                type:'GET',
                url:'{{route("customeradditional")}}',
                data : {id:listid},
                dataType : 'json',
                success : function(response) 
                {
                    //console.log(response.additonal);
                    $.each(response.additonal,function(key,value){

                        boxdata += '<div class="form-group row"><label class="col-md-3 col-form-label text-md-right"><b class="text-capitalize">'+key+'</b></label><div class="col-md-8 form-control">'+value+'</div></div></div>';
                    });
                    $("#displayadditional").html(boxdata);
                }
            });
        });
    }

     function table(){
        $("#user-customer").dataTable({
            'pageLength':10,
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    }
    */

</script>
@endsection

