<tr>
    <td>{{ $node->id }}</td>
    <td>{{ $node->title }}</td>
    <td>{{ $node->nodetype->label }}</td>
    <td>
        @if ($node->status == 'published')
            @if ($node->published_revision and ($node->latest_revision != $node->published_revision))
                <?php
                    $latestDraft = $node->fetchRevision($node->latest_revision);
                ?>
                <a rel="tooltip" title="Draft created {{ date('j/m/Y H:i:s', strtotime($latestDraft->created_at)) }}">Published with Draft</a>
            @else
                {{ ucfirst($node->status) }}
            @endif
        @else
            {{ ucfirst($node->status) }}
        @endif
    </td>
    <td>
        {{ @$node->owner->fullName }}
    </td>
    <td>{{ date('d M Y H:i', strtotime($node->updated_at)) }}</td>
    <td class="actions">
        @if (isset($node->branch_id) and $node->branch_id)
            @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.read'))
                <a href="{{ route('nodes.view', array($collection->application_id, $collection->id, $node->id, 'branch', $node->branch_id)) }}" class="btn btn-small"><i class="icon-search"></i> View</a>
            @endif

            @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.update'))
                <a href="{{ route('nodes.edit', array($collection->application_id, $collection->id, $node->id, 'branch', $node->branch_id)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
            @endif
        @else
            @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.read'))
                <a href="{{ route('nodes.view', array($collection->application_id, $collection->id, $node->id)) }}" class="btn btn-small"><i class="icon-search"></i> View</a>
            @endif

            @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.update'))
                <a href="{{ route('nodes.edit', array($collection->application_id, $collection->id, $node->id, $node->latest_revision)) }}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>
            @endif
        @endif
    </td>
</tr>