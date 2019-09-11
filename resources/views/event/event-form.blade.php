@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a href="{{route('home')}}" class="nav-link">Back Home</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('reminder')}}" class="nav-link">Back Event</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!-- end navbar -->

<div class="container">
    <!-- add list-->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Create Event</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="get_template">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Choose Template</label>
                            <div class="col-md-6">
                                <select class="form-control" name="template_list" id="display-template">
                                  <option>Choose</option>
                                  @if($templates->count() > 0)
                                    @foreach($templates as $row)
                                      <option value="{{$row->id}}">{{$row->name}}</option>
                                    @endforeach
                                  @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-warning btn-sm">Add</button>
                            </div>
                        </div> 
                    </form>

                     <form name="event_form" onsubmit="return validateForm()" method="POST" action="{{ route('addevent') }}">
                        @csrf

                         <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Lists Option</label>
                            <div class="col-md-6">
                                @foreach($data as $row)
                                <div class="form-check">
                                  <input class="form-check-input" name="id[]" type="checkbox" value="{{$row->id}}">
                                  <label class="form-check-label" for="{{$row->id}}">
                                    {{$row->name}}
                                  </label>
                                </div>
                                 @endforeach
                                <!-- end check box -->
                                 @if (session('error'))
                                    <div class="error">{{ session('error')->first('id') }}</div>
                                 @endif
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Message</label>
                            <div class="col-md-6">
                                <textarea id="divInput-description-post" class="form-control" name="message"></textarea>
                                 @if (session('error'))
                                    <div class="error">{{ session('error')->first('message') }}</div>
                                 @endif
                             </div>
                        </div> 

                         <div class="form-group row schedule">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Schedule Event</label>
                            <div class="col-md-6">
                                <input type="text" id="datetimepicker" class="form-control" name="event_date" />
                                 @if (session('error'))
                                    <div class="error">{{ session('error')->first('event_date') }}</div>
                                 @endif
                            </div>
                        </div>

                        <div class="form-group row">
                             <div class="col-md-4 text-md-right"><a class="btn btn-success btn-sm add-day">Add Day</a></div>
                            <div id="append" class="col-md-6">
                                 @if (session('error_day'))
                                  <div class="error">{{ session('error_day') }}</div>
                               @endif
                            </div>
                        </div>

                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Set Event
                                </button>
                            </div>
                        </div>
                     </form>
                     <!-- end form -->
                     
                </div>
            </div>
        </div>
    </div>
<!-- end container -->   
</div>


<!-- give emoji -->
 <script type="text/javascript">
    $("#divInput-description-post").emojioneArea({
        pickerPosition: "right",
        mainPathFolder : "{{url('')}}",
    });
</script>

<script type="text/javascript">

    /* Datetimepicker */
     $(function () {
          $('#datetimepicker').datetimepicker({
            format : 'YYYY-MM-DD HH:mm',
          });
      });

    $(document).ready(function(){
        displayTemplate();
        addDays();
        delDays();
    });

     function validateForm(){
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        var hh = today.getHours();
        hh = ("0" + hh).slice(-2);
        var min = today.getMinutes();
        min = ("0" + min).slice(-2);

        today = yyyy+'-'+mm+'-'+dd+' '+hh+':'+min;

        var ed = document.forms["event_form"];

        if(ed["event_date"].value == ""){
            alert("Date event must be filled out");
            return false;
        }

        if(ed["event_date"].value < today){
            alert("Date or time cannot be less than today");
            return false;
        }

     }

    function addDays(){
      $(".add-day").click(function(){
        var pos = $(".days").length;

        var box_html = '<select name="day[]" class="form-control col-sm-9 float-left days pos-'+pos+'"><?php for($x=-30;$x<=100;$x++) {
                echo "<option value=".$x.">$x</option>";
          }?></select><span><a id="pos-'+pos+'" class="btn btn-warning float-left del">Delete</a></span><div class="clearfix"></div>';

        $("#append").append(box_html);
      });
    }

    function delDays(){
      $("body").on("click",".del",function(){
        var pos = $(this).attr('id');
        $("."+pos).remove();
        $("#"+pos).remove();
      });
    }

    /* Attach broadcast template into textarea message */
    function displayTemplate(){
        $("body").on('submit','#get_template',function(e){
            e.preventDefault();
            var id = document.getElementsByName("template_list")[0].value;
            $.ajax({
                type : 'GET',
                url : '{{route("displaytemplate")}}',
                data : {'id':id},
                dataType : "text",
                success : function(txt){
                    $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(txt);
                }
            });
        });
    }

</script>
@endsection
