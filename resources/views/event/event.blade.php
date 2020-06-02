<div class="act-tel-tab">
  @if(count($data) > 0)
    @foreach($data as $row)
      <div class="bg-dashboard campaign row">
        <div class="col-lg-4 pad-fix col-card">
          <h5>
            <span class="campaignid-{{$row['id']}}">{{ $row['campaign_name'] }}</span>
            <span>
              <a data-name="{{ $row['campaign_name'] }}" id="{{ $row['id'] }}" class="edit icon-edit"></a>
            </span>  
          </h5> 

          <div class="notes">
            <div>Status : <color>@if($row['published'] == 0) draft @else published @endif</color></div>
            <div>List : {{ $row['label'] }}</div>
          </div>

          <div class="created">
            <!-- Created On :  $row['created_at'] -->
            @if($row['event_time'] == '-')
              Date Event :  -
            @else
              Date Event : <span class="campaign_event_id-{{$row['id']}} mr-1">{{ Date('M d, Y',strtotime($row['event_time'])) }}</span>
              <span>
                <a data-toggle="tooltip" data-toggle="tooltip" data-placement="right" title="Edit Event Date" data-name="{{ $row['event_time'] }}" id="{{ $row['id'] }}" class="edit_date icon-calendar"></a>
              </span> 
            @endif 
          </div>
        </div>

        <div class="col-lg-5 pad-fix mt-4">
          <div class="row">
             <!--  <div class="col-lg-3 pad-fix cardnumber">
                <div class="big-number">100</div>
                <div class="contact">Opened</div>
              </div> -->

              <div class="col-lg-4 pad-fix cardnumber">
                <div class="big-number">
                    <a class="contacts" href="{{url('add-message-event').'/'.$row['id']}}">{{ $row['total_template'] }}</a>
                </div>
                <div class="contact">Total Template</div>
              </div>

              <div class="col-lg-3 pad-fix cardnumber">
                <div class="big-number">
                    <a class="contacts" href="{{url('list-campaign').'/'.$row['id'].'/1/1'}}">{{ $row['total_message'] }}</a>
                </div>
                <div class="contact">Queue</div>
              </div>  
              <div class="col-lg-3 pad-fix cardnumber">
                <div class="big-number">
                    <a class="contacts" href="{{url('list-campaign').'/'.$row['id'].'/1/0'}}">{{ $row['sent_message'] }}</a>
                </div>
                <div class="contact">Delivered</div>
              </div> 
              
          </div>  
        </div>

        <div class="col-lg-3 pad-fix col-button">
          <!--
          <a href="{{url('report-reminder')}}" id="{{ $row['id'] }}" class="btn btn-warning btn-sm"><span class="icon-eye"></span></a>
          -->
            @if($row['published'] == 1)
              <button type="button" id="{{ $row['id'] }}"  class="btn btn-success event_duplicate" data-toggle="tooltip" title="Button Duplicate"><span class="icon-copy-text"></span></button>
            @else 
              <button type="button" id="{{ $row['id'] }}"  class="btn btn-primary published" data-toggle="tooltip" title="Button Publish">Publish</button>
            @endif
            <button type="button" id="{{ $row['id'] }}" class="btn btn-danger event-del" data-toggle="tooltip" data-toggle="tooltip" data-placement="top" title="Button Publish" title="Button Delete"><span class="icon-delete"></span></button>
            <div>
              <a href="{{url('add-message-event').'/'.$row['id']}}" class="btn btn-custom">
                Add & Edit Message
              </a>
            </div>
        </div>
      </div> 
    @endforeach

  <!-- PAGINATION -->
  <div class="paging">
    {{ $paginate }}
  </div>

  @endif

  <script type="text/javascript">
   $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip({
        'placement':'top'
      });   
   });
</script>
</div>
