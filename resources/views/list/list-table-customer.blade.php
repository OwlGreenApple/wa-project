@if($customer->count() > 0)
  <table class="table" id="data-customer">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>Additional</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      @foreach($customer as $col)
        <tr>
          <td>{{ $col->name }}</td>
          <td>{{ $col->email }}</td>
          <td>{{ $col->telegram_number }}</td>
          <td>
            @if( $col->additional <> null)
              <a additional="{{ $col->additional }}" class="btn btn-info btn-sm text-white view">View Addtional</a>
            @else
              -
            @endif
          </td>
          <td><a id="{{ $col->id }}" class="btn btn-danger btn-sm text-white del-customer">Delete</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
  
@else
  <h2>Add Your Contact</h2>
  <h6 class="mt-3">From <a id="tab-contact">Add Contact</a> or <a id="tab-form">Form</a></h6>
@endif