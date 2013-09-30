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

        <div class="btn-group pull-right">

            @if (Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodeType->name . '.revision-management'))
                @if ($node->latest_revision != $node->published_revision)
                    <a href="{{ route('nodes.publish', array(CORE_APP_ID, $collection->id, $node->id, $node->latest_revision)) }}" rel="tooltip" title="Publish" class="btn btn-small open-publish-node-modal"><i class="icon-level-up"></i></a>
                @endif
            @endif

            @if (isset($node->branch_id) and $node->branch_id)
                @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.read'))
                    <a rel="tooltip" title="View" href="{{ route('nodes.view', array($collection->application_id, $collection->id, $node->id, 'branch', $node->branch_id)) }}" class="btn btn-small"><i class="icon-search"></i></a>
                @endif

                @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.update'))
                    <a rel="tooltip" title="Edit" href="{{ route('nodes.edit', array($collection->application_id, $collection->id, $node->id, 'branch', $node->branch_id)) }}" class="btn btn-small"><i class="icon-edit"></i></a>
                @endif

                @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.delete'))
                    <a rel="tooltip" title="Delete" href="#deleteNodeModal"
                        data-application-id="{{ $collection->application_id }}"
                        data-collection-id="{{ $collection->id }}"
                        data-node-id="{{ $node->id }}"
                        data-branch-id="{{ $node->branch_id }}"
                        class="btn btn-small modal-toggle"><i class="icon-trash"></i></a>
                @endif
            @else
                @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.read'))
                    <a rel="tooltip" title="View" href="{{ route('nodes.view', array($collection->application_id, $collection->id, $node->id)) }}" class="btn btn-small"><i class="icon-search"></i></a>
                @endif

                @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.update'))
                    <a rel="tooltip" title="Edit" href="{{ route('nodes.edit', array($collection->application_id, $collection->id, $node->id, $node->latest_revision)) }}" class="btn btn-small"><i class="icon-edit"></i></a>
                @endif

                @if ( Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.' . $node->nodetype->name . '.delete'))
                    <a rel="tooltip" title="Delete" href="#deleteNodeModal" class="btn btn-small modal-toggle"
                            data-application-id="{{ $collection->application_id }}"
                            data-collection-id="{{ $collection->id }}"
                            data-node-id="{{ $node->id }}"
                            data-latest-revision="{{ $node->latest_revision }}"
                        ><i class="icon-trash"></i></a>
                @endif
            @endif

        </div>
    </td>
</tr>