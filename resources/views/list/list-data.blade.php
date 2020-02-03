@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-list-data">
  <div class="left">
    <h2>TEST LIST 1</h2>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container">
  <ul id="tabs" class="row">
      <li class="col-lg-4"><a id="tab1">Contact</a></li>
      <li class="col-lg-4"><a id="tab2">Add Contact</a></li>
      <li class="col-lg-4"><a id="tab3">Form</a></li>
  </ul>

  <!-- TABS CONTAINER -->
  <div class="tabs-content">
    <!-- TABS 1 -->
    <div class="tabs-container" id="tab1C">
      <div class="act-tel-tab">
          <h2>Add Your Contact</h2>
          <h6 class="mt-3">From <a>your contact</a> or <a>form</a></h6>

          <!-- if contact added successfully -->
          <table class="table table-bordered mt-4">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Phone</th>
                <th>Username</th>
                <th>Email</th>
              </tr>
            </thead>
          </table>
      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 2 -->
    <div class="tabs-container" id="tab2C">
      <div class="act-tel-tab">
        <div class="form-control wrapper message">
          Click "<b>import contact</b>" or "<b>take from group</b>" if you want
        </div>

        <form class="wrapper add-contact">
            <div class="form-group">
              <label>Name:</label>
              <input type="text" class="form-control" placeholder="Input Your Name" >
            </div>

            <div class="form-group">
              <label>No HP:</label>
              <input type="text" class="form-control" placeholder="6280000" />
              <i>*) format phone : 6280000</i>
            </div>

            <div class="form-group">
              <label>Telegram Username</label>
              <input type="text" class="form-control" placeholder="Input Your Telegram Username" />
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="text" class="form-control" placeholder="Input Your Email" />
            </div>

            <div class="text-right">
              <button type="submit" class="btn btn-custom">Add Contact</button>
            </div>
        </form>
      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 3 -->
    <div class="tabs-container" id="tab3C">
      <div class="act-tel-tab">
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

        <!-- last wrapper -->
        <div class="wrapper">
          <div class="form-group text-left">
             <label class="col-md-12 row">FORM URL&nbsp;&nbsp;<span class="icon-copy"></span></label>
             <input type="text" class="form-control-lg" value="http://activtele.com/zkkdai"/>
          </div>
          
          <div class="form-group text-left">
             <label>COPY / PASTE on your Site&nbsp;&nbsp;<span class="icon-copy"></span></label>
             <textarea class="form-control"><!-- Facebook Pixel Code -->
                    <script>
                     !function(f,b,e,v,n,t,s)
                     {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                     n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                     if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                     n.queue=[];t=b.createElement(e);t.async=!0;
                     t.src=v;s=b.getElementsByTagName(e)[0];
                     s.parentNode.insertBefore(t,s)}(window, document,'script',
                     'https://connect.facebook.net/en_US/fbevents.js');
                     fbq('init', '2534191586629463');
                     fbq('track', 'PageView');
                    </script>
                    <noscript><img height="1" width="1" style="display:none"
                     src="https://www.facebook.com/tr?id=2534191586629463&ev=PageView&noscript=1"
                    /></noscript>
                    <!-- End Facebook Pixel Code -->  
              </textarea>
          </div>
        </div>
        <!-- end last wrapper -->

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
