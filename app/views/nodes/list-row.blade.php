<tr>
    <td>{{ $node->id }}</td>
    <td>{{ $node->title }}</td>
    <td>{{ $node->nodetype->label }}</td>
    <td>{{ ucfirst($node->status) }}</td>
    <td>{{ @$node->owner->fullName }}</td>
    <td>{{ date('j-m-Y H:i', strtotime($node->created_at)) }}</td>
    <td class="actions">
        @if (isset($node->branch_id) and $node->branch_id)
            <a href="{{ route('nodes.view', array($collection->id, $node->id, 'branch', $node->branch_id)) }}" class="btn btn-small"><i class="icon-search"></i> View</a>
            <a href="{{ route('nodes.edit', array($collection->id, $node->id, 'branch', $node->branch_id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
        @else
            <a href="{{ route('nodes.view', array($collection->id, $node->id)) }}" class="btn btn-small"><i class="icon-search"></i> View</a>
            <a href="{{ route('nodes.edit', array($collection->id, $node->id, $node->latest_revision)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
        @endif
    </td>
</tr>