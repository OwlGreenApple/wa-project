 @if(count($data) > 0)
    @foreach($data as $row)
      <div class="bg-dashboard campaign row">
      <div class="col-lg-4 pad-fix col-card">
        <h5><span class="campaignid-{{$row['campaign_id']}}">{{ $row['name'] }}</span>
            <span>
              <a data-name="{{ $row['name'] }}" id="{{ $row['campaign_id'] }}" class="edit icon-edit"></a>
            </span>  
        </h5>                           
        <div class="notes">
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
              <a class="contacts" href="{{ url('list-apt') }}/{{ $row['campaign_id'] }}" target="_blank">
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
          <a class="btn btn-warning btn-sm text-white" href="{{ url('form-apt') }}/{{ $row['campaign_id'] }}" target="_blank"><span class="icon-eye"></span></a>
          <a href="{{url('edit-apt')}}/{{ $row['campaign_id'] }}" class="btn btn-edit btn-sm" target="_blank"><span class="icon-edit"></span></a>
          <a id="{{ $row['campaign_id'] }}" class="btn btn-success btn-sm" target="_blank"><span class="icon-export"></span></a>
          <button type="button" id="{{ $row['campaign_id'] }}" class="btn btn-danger btn-sm appt-del"><span class="icon-delete"></span></button>
      </div>
  </div>   
  @endforeach
 @else
  <h5 class="text-center">Currently you don't have any appointments, please click 'Create Appointment'.</h5>
 @endif