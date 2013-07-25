<ol class="dd-list">
    @foreach ($branches as $branch)
        <li class="dd-item" data-id="{{ $branch->id }}">
            <div class="pull-right node-hierarchy-buttons">
                {{ $branch->node->statusBadge }}
                <div class="btn-group">
                    <a href="#" rel="tooltip" title="View" class="btn btn-mini"><i class="icon-search"></i></a>
                    <a href="#" rel="tooltip" title="Edit" class="btn btn-mini"><i class="icon-edit"></i></a>
                    <a href="#" rel="tooltip" title="Add Link" class="btn btn-mini"><i class="icon-link"></i></a>
                    <a href="#" rel="tooltip" title="Remove Link" class="btn btn-mini"><i class="icon-unlink"></i></a>
                </div>
            </div>
            <div class="dd-handle">
                {{ $branch->node->title }}
            </div>

            @if (count($branch->getChildren()))
                @include('nodes.branch', array('branches' => $branch->getChildren()))
            @endif
        </li>
    @endforeach
</ol>