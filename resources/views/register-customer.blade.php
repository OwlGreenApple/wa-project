@extends('layouts.customer')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
    
                <div class="col-md-12">
                    <?php echo $content;?>
                </div>

                <div class="card-body">
                    <div class="error_message"></div>
                    <form id="addcustomer">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-7">
                                <input id="name" type="text" class="form-control" name="name" />
                                <span class="error name"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('WA Number') }}</label>

                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input name="code_country" class="form-control" data-countryCode="ID" value="+62" readonly/>
                                        <span class="error code_country"></span>
                                    </div>
                                    <!-- end select -->    
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="wa_number" />
                                        <span class="error wa_number"></span>
                                    </div>
                                <!-- end row -->    
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <input type="hidden" name="listname" value="{{$listname}}"/>
                            </div>
                            <div class="col-md-6">
                                <span class="error error_list"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Thank You</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p><!-- message here --></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
         addCustomer();
    });

    function addCustomer(){
        $("#addcustomer").submit(function(e){
            e.preventDefault();
            var data = $(this).serialize();
             $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
            $.ajax({
                type : "POST",
                url : "{{ route('addcustomer') }}",
                data : data,
                success : function(result){
                    if(result.success == true){
                        $(".modal-body > p").text(result.message);
                        getModal();
                        clearField();
                    } else {
                        $(".name").text(result.name);
                        $(".wa_number").text(result.wa_number);
                        $(".code_country").text(result.code_country);
                        $(".error_message").text(result.message);
                        $(".error_list").text(result.list);
                    }
                }
            });
            /*end ajax*/
        });
    }

    /* Display modal when customer has finished registering */
    function getModal(){
        $("#myModal").modal()
    }

    /* Clear / Empty fields after ajax reach success */
    function clearField(){
        $("input[name='name'],input[name='wa_number']").val('');
        $(".error").html('');
    }
</script>

@endsection
