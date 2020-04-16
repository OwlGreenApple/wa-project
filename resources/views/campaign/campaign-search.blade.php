<!-- tab 1 -->
@if(count($data) > 0)
  @foreach($data as $index =>$row)
    @if($row['type'] == 2)
    <!-- broadcast -->
      <div class="bg-dashboard campaign row">
        <div class="col-lg-4 pad-fix col-card">
          <h5>{{ $row['campaign'] }}</h5>
          <div class="notes">
            <div>Type Campaign : <color><span class="gr">Broadcast</span></color></div>
            <div>Schedule post : <b>{{ $row['day_send'] }} {{ $row['sending'] }}</b></div>
            @if($row['label'] !== null)
              <div>List : {{ $row['label'] }}</div>
            @elseif($row['label'] == null && $row['group_name'] !== null)
              <div>Telegram Group : {{ $row['group_name'] }}</div>
            @elseif($row['label'] == null && $row['channel'] !== null)
              <div>Telegram Channel : {{ $row['channel'] }}</div>
            @endif
          </div>
          <div class="created">
            Created On : {{ $row['created_at'] }}
          </div>
        </div>

        <div class="col-lg-5 pad-fix mt-4">
          <div class="row">
              @if($row['label'] !== null)
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    <a class="contacts" href="{{url('list-campaign').'/'.$row['campaign_id'].'/broadcast/0'}}">{{ $row['total_message'] }}</a>
                  </div>
                  <div class="contact">Queue</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    <a class="contacts" href="{{url('list-campaign').'/'.$row['campaign_id'].'/broadcast/1'}}">{{ $row['sent_message'] }}</a>
                  </div>
                  <div class="contact">Delivered</div>
                </div> 
              @endif
              <!--
              <div class="col-lg-3 pad-fix cardnumber">
                <div class="big-number">9</div>
                <div class="contact">Opened</div>
              </div>
              <div class="col-lg-3 pad-fix cardnumber">
                <div class="big-number">9%</div>
                <div class="contact">Open Rate</div>
              </div>
              -->
          </div>  
        </div>

        <div class="col-lg-3 pad-fix col-button">
          <button id="{{ $row['id'] }}" type="button" class="btn btn-success broadcast_duplicate" data-toggle="tooltip" title="Button Duplicate"><span class="icon-copy-text"></span><span class="tooltipstered" title="Duplicate Campaign"></span></button>
          <button id="{{ $row['id'] }}" type="button" class="btn btn-danger broadcast-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
          <div>
            <a title="Edit Message" data-toggle="tooltip" id="{{ $row['id'] }}" data-name="{{ $row['campaign'] }}" data-date="{{ $row['date_send'] }}" data-message="{{ $row['messages'] }}" data-time="{{ $row['sending'] }}" type="button" class="btn btn-custom edit_campaign">Edit</a>
          </div>

        </div>
      </div> 

    @else
    <!-- reminder -->
      <div class="bg-dashboard campaign row">
          <div class="col-lg-4 pad-fix col-card">
            <h5>{{ $row['campaign_name'] }}</h5>                                                
            <div class="notes">
              @if($row['type'] == 0)
                <div>Type Campaign : <color>Event</color></div>
              <!--   <div>Schedule post : <b>{{ $row['sending'] }}</b></div>
                <div>Time : <b>{{ $row['sending_time'] }}</b></div> -->
              @elseif($row['type'] == 1)
                <div>Type Campaign : <color><span class="og">Auto schedule</span></color></div>
              @endif
              <div>List : {{ $row['label'] }}</div>
            </div>
            <div class="created">
              Created On : {{ $row['created_at'] }}
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
                     @if($row['type'] == 0)
                      <a class="contacts" href="{{url('add-message-event').'/'.$row['id']}}">{{ $row['total_template'] }}</a>
                    @elseif($row['type'] == 1)
                      <a class="contacts" href="{{url('add-message-auto-responder').'/'.$row['id']}}">{{ $row['total_template'] }}</a>
                    @endif
                  </div>
                  <div class="contact">Total Template</div>
                </div>

                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    @if($row['type'] == 0)
                      <a class="contacts" href="{{url('list-campaign').'/'.$row['id'].'/1/1'}}">{{ $row['total_message'] }}</a>
                    @elseif($row['type'] == 1)
                      <a class="contacts" href="{{url('list-campaign').'/'.$row['id'].'/0/1'}}">{{ $row['total_message'] }}</a>
                    @endif
                  </div>
                  <div class="contact">Queue</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    @if($row['type'] == 0)
                      <a class="contacts" href="{{url('list-campaign').'/'.$row['id'].'/1/0'}}">{{ $row['sent_message'] }}</a>
                    @elseif($row['type'] == 1)
                      <a class="contacts" href="{{url('list-campaign').'/'.$row['id'].'/0/0'}}">{{ $row['sent_message'] }}</a>
                    @endif
                  </div>
                  <div class="contact">Delivered</div>
                </div> 
                
            </div>  
          </div>

          <div class="col-lg-3 pad-fix col-button">
            <!--
            <a href="{{url('report-reminder')}}" id="{{ $row['id'] }}" class="btn btn-warning btn-sm"><span class="icon-eye"></span></a>
            -->  
            @if($row['type'] == 0)
              <button type="button" id="{{ $row['id'] }}"  class="btn btn-success event_duplicate" data-toggle="tooltip" data-placement="top" title="Button Duplicate"><span class="icon-copy-text"></span></button>
              <button type="button" id="{{ $row['id'] }}" class="btn btn-danger event-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
              <div>
                <a href="{{url('add-message-event').'/'.$row['id']}}" class="btn btn-custom">Add / Edit</a>
              </div>
            @elseif($row['type'] == 1)
              <!-- <button id="{{ $row['id'] }}" type="button" class="btn btn-success btn-sm responder_duplicate"><span class="icon-copy-text"></span></button> -->
              <button id="{{ $row['id'] }}" type="button" class="btn btn-danger responder-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
              <div>
                <a href="{{url('add-message-auto-responder').'/'.$row['id']}}" class="btn btn-custom">Add / Edit</a>
              </div>
            @endif

          </div>
      </div> 

    @endif
  @endforeach
@endif

<script type="text/javascript">
   $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip({
        'placement':'top'
      });   
   });
</script>