@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>LISTS</h2>
  </div>

  <div class="act-tel-dashboard-right">
     <a href="{{url('createlists')}}" class="btn btn-custom">Create List</a>
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

<!-- Modal Import Contact -->
  <div class="modal fade child-modal" id="edit-contact" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
           
          <div class="act-tel-tab">
              <div class="wrapper">
                <div class="form-control col-lg-6 message">
                  <sb>Saved, click to copy link from</sb> <a class="icon-copy"></a>
                </div>
              </div>

            <form class="form-contact" id="save-list">
              <div class="wrapper">
                <div class="form-contact" id="save-list">
                  <div class="input-group form-group">
                    <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
                  </div>

                  <div class="input-group form-group">
                      <input type="text" name="labelname" class="form-control" value="" placeholder="Input List name" >
                  </div> 
              </div>
              <!-- end wrapper -->

               <!-- outer wrapper -->
              <div class="outer-wrapper">
                <div class="form-row">
                  <div class="form-group col-md-3 py-2">
                    <h6>Custom Fields</h6>
                  </div>

                  <div class="form-group col-md-8">
                    <div class="relativity">
                       <select id="type_fields" class="form-control custom-select">
                          <option value="1">Fields</option>
                          <option value="2">Dropdown</option>
                       </select>
                       <span class="icon-carret-down-circle"></span>
                    </div>
                  </div>
                  <div class="form-group col-md-1">
                    <button type="button" class="btn btn-form add-field"><span class="icon-add"></span></button>
                  </div>
                </div>

                <div id="append" class="form-row">
                   <!-- display input here -->
                </div> 

              </div>
              <!-- end outer wrapper -->

              <!-- middle wrapper -->
              <div class="wrapper">
                <div class="form-group text-left">
                   <label>Pixel</label>
                   <textarea name="pixel" class="form-control"></textarea>
                </div>
                
                <div class="text-right">
                  <button type="submit" class="btn btn-custom">Save Form</button>
                </div>

              </form>

        </div>
      </div>
      
    </div>
  </div>
  <!-- End Modal -->

<script type="text/javascript">
  $(document).ready(function(){
    displayData();
    searchList();
    deleteList();
    editList();
  });

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

  function editList(){
    $("body").on("click",".btn-edit",function(){
      $("#edit-contact").modal();
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
          success : function(result){
            alert(result.message);
            displayData();
          }
        });
      } else {
        return false;
      }
     
    });
  }
</script>
@endsection

