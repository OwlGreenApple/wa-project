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
            <div>Schedule post : <b>{{ $row['day_send'] }}</b></div>
            <div>Time : <b>{{ $row['sending'] }}</b></div>
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
                  <div class="big-number">{{ $row['total_message'] }}</div>
                  <div class="contact">Message</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">{{ $row['sent_message'] }}</div>
                  <div class="contact">Send</div>
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
          <a id="{{ $row['id'] }}" data-name="{{ $row['campaign'] }}" data-date="{{ $row['date_send'] }}" data-message="{{ $row['messages'] }}" data-time="{{ $row['sending'] }}" type="button" class="btn btn-activ btn-sm edit_campaign"><span class="icon-edit"></span></a>
          <button id="{{ $row['id'] }}" type="button" class="btn btn-success btn-sm broadcast_duplicate" data-toggle="tooltip" data-placement="top" title="Button Duplicate"><span class="icon-copy-text"></span></button>
          <button id="{{ $row['id'] }}" type="button" class="btn btn-danger btn-sm broadcast-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
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
              @elseif($row['type'] == 1)
                <div>Type Campaign : <color><span class="og">Auto schedule</span></color></div>
              @endif
              <div>Schedule post : <b>{{ $row['sending'] }}</b></div>
              <div>Time : <b>{{ $row['sending_time'] }}</b></div>
              <div>List : {{ $row['label'] }}</div>
            </div>
            <div class="created">
              Created On : {{ $row['created_at'] }}
            </div>
          </div>

          <div class="col-lg-5 pad-fix mt-4">
            <div class="row">
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">{{ $row['total_message'] }}</div>
                  <div class="contact">Message</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">{{ $row['sent_message'] }}</div>
                  <div class="contact">Send</div>
                </div> 
                <!--<div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100</div>
                  <div class="contact">Opened</div>
                </div>
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100%</div>
                  <div class="contact">Open Rate</div>
                </div>-->
            </div>  
          </div>

          <div class="col-lg-3 pad-fix col-button">
            <!--
            <a href="{{url('report-reminder')}}" id="{{ $row['id'] }}" class="btn btn-warning btn-sm"><span class="icon-eye"></span></a>
            -->  
            @if($row['type'] == 0)
              <button type="button" id="{{ $row['id'] }}"  class="btn btn-success btn-sm event_duplicate" data-toggle="tooltip" data-placement="top" title="Button Duplicate"><span class="icon-copy-text"></span></button>
              <button type="button" id="{{ $row['id'] }}" class="btn btn-danger btn-sm event-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
              <div>
                <a href="{{url('add-message-event').'/'.$row['id']}}" class="btn btn-custom">Add Message</a>
              </div>
            @elseif($row['type'] == 1)
              <!-- <button id="{{ $row['id'] }}" type="button" class="btn btn-success btn-sm responder_duplicate"><span class="icon-copy-text"></span></button> -->
              <button id="{{ $row['id'] }}" type="button" class="btn btn-danger btn-sm responder-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
              <div>
                <a href="{{url('add-message-auto-responder').'/'.$row['id']}}" class="btn btn-custom">Add Message</a>
              </div>
            @endif

          </div>
      </div> 

    @endif
  @endforeach
@endif