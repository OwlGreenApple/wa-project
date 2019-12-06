<div class="table-responsive">
<table class="table table-striped table-responsive" id="reminder-message">
    <thead>
        <th>lists name</th>
        <th>lists url</th>
        <th>Days</th>
        <th>Message</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Action</th>
    </thead>
    <tbody>
        @php $deleteremove = true; @endphp
        @if($data->count() > 0)
        @foreach($data as $row)
            <tr>
                <td>{{$row->label}}</td>
                <td>{{$row->name}}</td>
                <td> 
                    <span class="get-day-{{$row->id}}">{{$row->days}}
                    </span>
                    <!--<div class="mt-1"><small><a class="display_days" id="{{$row->id}}">Edit</a></small></div>-->
                </td>
                <td class="wraptext">
                    <span class="get-text-{{$row->id}}">{{$row->message}}</span>
                    <div><small><a id="{{$row->id}}" class="display_popup">Read More</a></small></div>
                </td>
                <td>{{$row->created_at}}</td>
                <td>{{$row->updated_at}}</td>
                <td>
                    <a href="{{url('reminder-status/'.$row->id.'/'.$row->status.'')}}" class="btn btn-primary btn-sm"> @if($row->status == 0)
                        Run
                    @else
                        Pause
                    @endif</a>

                    <?php
                        if($row->list_id == 17 || $row->list_id == 18)
                        {
                            $deleteremove = true;
                        }
                        else
                        {
                            $deleteremove = false;
                        }
                    ?>

                    @if($deleteremove == false)
                    <a id="{{$row->id}}" class="btn btn-danger btn-sm del-col">Delete</a>
                    @endif

                     <a id="{{encrypt($row->list_id)}}" class="btn btn-info btn-sm download-col">Download CSV</a>
                </td>
            </tr>
        @endforeach
        @endif
    </tbody>
</table>