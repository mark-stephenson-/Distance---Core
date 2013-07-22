<tr>
    <td>{{ $node->id }}</td>
    <td>{{ $node->title }}</td>
    <td>{{ $node->nodetype->label }}</td>
    <td>{{ $node->status }}</td>
    <td>{{ @$node->owner->fullName }}</td>
    <td>{{ date('j-m-Y H:i', strtotime($node->created_at)) }}</td>
    <td class="actions">
        {{--
        @if (isset($node->branch_id) and $node->branch_id)
            <a href="{{ route('nodes.view', array($node->id, 'branch', $node->branch_id)) }}" class="button white"><i class="icon-search"></i> View</a>
            <a href="{{ route('nodes.edit', array($node->id, 'branch', $node->branch_id)) }}" class="button white"><i class="icon-pencil"></i> Edit</a>
        @else
            <a href="{{ route('nodes.view', array($node->id)) }}" class="button white"><i class="icon-search"></i> View</a>
            <a href="{{ route('nodes.edit', array($node->id, $node->current_revision)) }}" class="button white"><i class="icon-pencil"></i> Edit</a>
        @endif
        --}}
    </td>
</tr>