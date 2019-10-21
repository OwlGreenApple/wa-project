<div class="table-responsive">
    <h4>Event Schedule</h4>
    <table class="table table-striped table-responsive" id="event-list">
        <thead>
            <th>Event Label</th>
            <th>Event URL</th>
            <th>Event Date</th>
            <th>Amount Days to Send</th>
            <th>Sending Hour</th>
            <th>Message</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Action</th>
        </thead>
        <tbody>
            @if($event->count() > 0)
            @foreach($event as $rows)
                <tr>
                    <td>{{$rows->label}}</td>
                    <td>{{$rows->name}}</td>
                    <td>{{$rows->event_date}}</td>
                    <td>{{$rows->days}}</td>
                    <td>{{$rows->hour_time}}</td>
                    <td class="wraptext">
                        <span class="get-text-{{$rows->id}}">{{$rows->message}}</span>
                        <div><small><a id="{{$rows->id}}" class="display_popup">Read More | Edit</a></small></div>
                    </td>
                    <td>{{$rows->created_at}}</td>
                    <td>{{$rows->updated_at}}</td>
                    <td>
                        <a href="{{url('eventstatus/'.$rows->id.'/'.$rows->status.'')}}" class="btn btn-primary btn-sm"> @if($rows->status == 0)
                            Run
                        @else
                            Pause
                        @endif</a>
                        <!-- edit button -->
                        <div class="mt-1"><a class="btn btn-warning btn-sm edit-col @if($rows->status == 1) disabled @endif" @if($rows->status == 0) id="{{$rows->id}}" @endif >Edit</a></div>
                        <div class="mt-1"><a class="btn btn-danger btn-sm del-col" id="{{$rows->id}}">Delete</a></div>
                        <div class="mt-1"><a class="btn btn-success btn-sm download-col" id="{{encrypt($rows->list_id)}}">Download CSV</a></div> 
                    </td>
                </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>