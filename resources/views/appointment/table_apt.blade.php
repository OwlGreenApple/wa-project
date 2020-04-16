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
            <div class="col-lg-4 pad-fix cardnumber">
                <div class="big-number">
                  <a class="contacts" href="{{url('edit-apt')}}/{{ $row['campaign_id'] }}">
                    {{$row['total_template']}}
                  </a>
                </div>
                <div class="contact">Total Template</div>
            </div>  
            <div class="col-lg-3 pad-fix cardnumber">
              <div class="big-number">
                <a class="contacts" href="{{ url('list-apt') }}/{{ $row['campaign_id'] }}/1">
                  {{$row['total_message']}}
                </a>
              </div>
              <div class="contact">Queue</div>
            </div> 
            <div class="col-lg-3 pad-fix cardnumber">
              <div class="big-number">
                <a class="contacts" href="{{ url('list-apt') }}/{{ $row['campaign_id'] }}/0">
                  {{$row['total_sent']}}
                </a>
              </div>
              <div class="contact">Delivered</div>
            </div> 
            
        </div>  
      </div>

      <div class="col-lg-3 pad-fix col-button">
          <a class="btn btn-custom btn-sm text-white mt-0" href="{{ url('form-apt') }}/{{ $row['campaign_id'] }}">Create appointment</a>
          <a href="{{url('edit-apt')}}/{{ $row['campaign_id'] }}" data-toggle="tooltip" title="Add / Edit Appointment" class="btn btn-edit btn-sm"><span class="icon-edit"></span></a>
          <a href="{{url('export_csv_appt')}}/{{ $row['campaign_id'] }}" class="btn btn-success btn-sm" data-toggle="tooltip" title="Export XLSX"><span class="icon-export"></span></a>
          <button data-toggle="tooltip" title="Delete Appointment" type="button" id="{{ $row['campaign_id'] }}" class="btn btn-danger btn-sm appt-del"><span class="icon-delete"></span></button>
      </div>
  </div>   
  @endforeach
 @else
  <h5 class="text-center">Currently you don't have any appointments, please click 'Create Appointment'.</h5>
 @endif

 <script type="text/javascript">
   $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip({
        'placement':'top'
      });   
   });
</script>