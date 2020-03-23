 @if(count($data) > 0)
    @foreach($data as $row)
      <div class="bg-dashboard campaign row">
      <div class="col-lg-4 pad-fix col-card">
        <h5>{{ $row['name'] }}
            <span>
              <a data-link="{{env('APP_URL')}}xyz" class="btn-copy icon-copy"></a>
            </span>  
        </h5>                           
        <div class="notes">
          <a href="{{ url('form-apt') }}">See Form</a>
          <!-- <div class="link_wrap">Link From : {{env('APP_URL')}}{{ $row['url'] }}
            <span>
              <a data-link="{{env('APP_URL')}}{{$row['url']}}" class="btn-copy icon-copy"></a>
            </span>
          </div> -->
          
          <div>List : {{$row['label']}}</div>
        </div>
        <div class="created">
          Create On : {{$row['created_at']}}
        </div>
      </div>

      <div class="col-lg-5 pad-fix mt-2">
        <div class="row">
            <div class="col-lg-3 pad-fix cardnumber">
            &nbsp
            </div>  
            <div class="col-lg-3 pad-fix cardnumber">
              <a href="{{ url('list-apt') }}/{{ $row['campaign_id'] }}" target="_blank">
                <div class="big-number">{{$row['contacts']}}</div>
                <div class="contact">Contact</div>
              </a>
            </div>  
            <!--<div class="col-lg-3 pad-fix cardnumber">
              <div class="big-number">7</div>
              <div class="contact">Send</div>
            </div> 
            -->
        </div>  
      </div>

      <div class="col-lg-3 pad-fix col-button">
          <a href="{{url('edit-apt')}}/{{ $row['campaign_id'] }}" class="btn btn-edit btn-sm" target="_blank"><span class="icon-edit"></span></a>
          <a id="{{ $row['campaign_id'] }}" class="btn btn-success btn-sm" target="_blank"><span class="icon-export"></span></a>
          <button type="button" id="{{ $row['campaign_id'] }}" class="btn btn-danger btn-sm campaign-del"><span class="icon-delete"></span></button>
      </div>
  </div>   
    @endforeach
 @endif