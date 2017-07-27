<ol class="dd-list">
    @foreach ($branches as $branch)
        <li class="dd-item" data-id="{{ $branch->id }}">
            <div class="pull-right node-hierarchy-buttons">
                <small class="muted"><em>{{ $nodeTypes[$branch->node->node_type]->label }}</em></small> {{ $branch->node->statusBadge }}
                <div class="btn-group">
                    <a href="{{ route('questionnaires.view', array($branch->node->id, 'branch', $branch->id)) }}" rel="tooltip" title="View" class="btn btn-mini"><i class="icon-search"></i></a>
                    @if ($branch->node->status == 'draft' )
                        <a href="{{ route('questionnaires.edit', array($branch->node->id, 'branch', $branch->id)) }}" rel="tooltip" title="Edit" class="btn btn-mini"><i class="icon-edit"></i></a>
                        <a href="{{ route('questionnaires.delete', array($branch->node->id, 'branch', $branch->id)) }}" rel="tooltip" title="Delete" class="btn btn-mini" onclick="return confirm('Are you sure you want to delete this question?')"><i class="icon-trash"></i></a>
                    @endif
                </div>
            </div>
            <div class="dd-handle">
                {{ $branch->node->title }}
            </div>

            @if (count($branch->getChildren()))
                @include('nodes.branch', array('branches' => $branch->getChildren(), 'nodeTypes' => $nodeTypes))
            @endif
        </li>
    @endforeach
</ol>