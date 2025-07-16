<?php

namespace Pterodactyl\Http\Controllers\Admin\Servers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Models\Egg;
use Pterodactyl\Models\Node;
use Pterodactyl\Models\Location;
use Pterodactyl\Services\Servers\ServerCreationService;
use Prologue\Alerts\AlertsMessageBag;

class ServerTemplateController extends Controller
{
    public function __construct(
        private AlertsMessageBag $alert,
        private ServerCreationService $creationService,
    ) {
    }

    /**
     * Display a listing of server templates.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $templates = \Pterodactyl\Models\ServerTemplate::all();

        return view('admin.servers.templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Create a new server from template.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request): RedirectResponse
    {
        $templateId = $request->input('egg');
        $template = \Pterodactyl\Models\ServerTemplate::findOrFail($templateId);

        $egg = Egg::findOrFail($template->egg_id);

        $location = Location::first();
        $node = Node::first();

        // configから値を取得（なければデフォルト）
        $config = $template->config ?? [];
        $memory = $config['memory'] ?? 1024;
        $disk = $config['disk'] ?? 10240;
        $cpu = $config['cpu'] ?? 100;

        // 空いているポートをランダムで割り当て
        $allocationId = $this->getRandomAvailableAllocationId($node);

        $data = [
            'name' => $template->name . '-' . date('YmdHis'),
            'owner_id' => 1,
            'egg_id' => $egg->id,
            'nest_id' => $egg->nest_id,
            'node_id' => $node ? $node->id : null,
            'location_id' => $location ? $location->id : null,
            'memory' => $memory,
            'disk' => $disk,
            'cpu' => $cpu,
            'swap' => 0,
            'io' => 500,
            'startup' => $egg->startup,
            'image' => $egg->docker_image,
            'environment' => [],
            'allocation_id' => $allocationId,
            'start_on_completion' => true,
        ];

        $server = $this->creationService->handle($data);

        $this->alert->success('サーバーがテンプレートから作成されました')->flash();

        return redirect('/admin/servers/view/' . $server->id);
    }

    /**
     * 空いているポートをランダムで取得
     */
    private function getRandomAvailableAllocationId($node)
    {
        if (!$node) return null;
        $allocations = $node->allocations()->where('server_id', null)->get();
        if ($allocations->isEmpty()) return null;
        return $allocations->random()->id;
    }
}
