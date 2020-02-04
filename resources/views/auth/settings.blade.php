@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>SETTINGS</h2>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container">
  <ul id="tabs" class="row">
      <li class="col-lg-4"><a id="tab1">Telegram Settings</a></li>
      <li class="col-lg-4"><a id="tab2">Account Settings</a></li>
  </ul>

  <!-- TABS CONTAINER -->
  <div class="tabs-content">

    <!-- TABS 2 -->
    <div class="tabs-container" id="tab1C">
      <div class="act-tel-settings">
        <div class="form-control wrapper message">
          Your Telegram Account Connected!
        </div>

        <form class="wrapper add-contact">
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Phone Telegram :</label>
              <input type="text" class="form-control col-sm-9" />
            </div>

            <div class="text-right">
              <button type="submit" class="btn btn-custom">Connect</button>
            </div>
        </form>

        <div class="wrapper add-contact">
           <div class="form-group row">
              <label class="col-sm-3 col-form-label">Phone Telegram :</label>
              <input type="text" class="form-control col-sm-9" />
            </div>

            <div class="text-right">
              <button type="submit" class="btn btn-custom">Connect</button>
            </div>
        </div>

      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 3 -->
    <div class="tabs-container" id="tab2C">
      <div class="act-tel-settings">
        <div class="wrapper">
          <div class="form-control col-lg-6 message">
            <sb>Saved, click to copy link from</sb> <a class="icon-copy"></a>
          </div>
        </div>

        <div class="wrapper">
          <form class="form-contact">
            <div class="input-group form-group">
                <input type="text" class="form-control" placeholder="Click to add message" >
                <span class="input-group-append">
                  <div class="btn border-left-0 border edit-icon">
                      <span class="icon-edit"></span>
                  </div>
                </span>
            </div>

            <div class="input-group form-group">
                <label></label>
                <input type="text" class="form-control" placeholder="Input your name" >
            </div> 

            <div class="input-group form-group">
                <input type="text" class="form-control" placeholder="Input your phone" >
            </div> 

            <div class="input-group form-group">
                <input type="text" class="form-control" placeholder="Input your Telegram username" >
            </div>

            <div class="input-group form-group">
                <input type="text" class="form-control" placeholder="Email" >
            </div>
        </div>
        <!-- end wrapper -->

         <!-- outer wrapper -->
        <div class="outer-wrapper">
          <div class="form-row">
            <div class="form-group col-md-11">
              <input type="text" class="form-control" placeholder="Custom fields"/>
            </div>
            <div class="form-group col-md-1">
              <button type="button" class="btn btn-form"><span class="icon-delete"></span></button>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
               <select class="custom-select form-control relativity">
                  <option>Type</option>
                  <option>...</option>
               </select>
               <span class="icon-carret-down-circle"></span>
            </div>
            <div class="form-group col-md-9">
              <input type="text" class="form-control" placeholder="Field Names"/>
            </div>
            <div class="form-group col-md-1">
              <button type="button" class="btn btn-form"><span class="icon-add"></span></button>
            </div>
          </div>

        </div>
        <!-- end outer wrapper -->

        <!-- middle wrapper -->
        <div class="wrapper">
          <div class="form-group text-left">
             <label>Pixel</label>
             <textarea class="form-control"></textarea>
          </div>
          
          <div class="text-right">
            <button type="submit" class="btn btn-custom">Save Form</button>
          </div>

        </form>
        </div>
        <!-- end middle wrapper -->

      </div>
    <!-- end tabs -->    
    </div>

  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {    
     tabs();
  });

  // Jquery Tabs
  function tabs() {    
      $('#tabs li a:not(:first)').addClass('inactive');
      $('.tabs-container').hide();
      $('.tabs-container:first').show();
          
      $('#tabs li a').click(function(){
          var t = $(this).attr('id');
        if($(this).hasClass('inactive')){ //this is the start of our condition 
          $('#tabs li a').addClass('inactive');           
          $(this).removeClass('inactive');
          
          $('.tabs-container').hide();
          $('#'+ t + 'C').fadeIn('slow');
       }
      });
  }

</script>

@endsection
