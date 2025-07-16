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
        // ハードコードされたテンプレートデータ
        $templates = [
            [
                'id' => 1,
                'name' => 'Minecraft',
                'description' => 'Minecraft サーバーテンプレート',
                'memory' => 2048,
                'disk' => 10240,
                'cpu' => 200,
            ],
            [
                'id' => 2,
                'name' => 'Rust',
                'description' => 'Rust サーバーテンプレート',
                'memory' => 4096,
                'disk' => 20480,
                'cpu' => 300,
            ],
        ];

        // オーナー一覧
        $owners = \Pterodactyl\Models\User::all();

        return view('admin.servers.templates.index', [
            'templates' => $templates,
            'owners' => $owners,
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
        $eggId = $request->input('egg');
        $ownerId = $request->input('owner_id');
        $egg = Egg::findOrFail($eggId);

        // 必要なパラメータを取得（例: location_id, node_id, name）
        $location = Location::first();
        $node = Node::first();

        // 空いているポートからランダムに選択
        $allocation = \Pterodactyl\Models\Allocation::whereNull('server_id')->inRandomOrder()->first();

        $data = [
            'name' => $egg->name . '-' . date('YmdHis'),
            'owner_id' => $ownerId,
            'egg_id' => $egg->id,
            'nest_id' => $egg->nest_id,
            'node_id' => $node ? $node->id : null,
            'location_id' => $location ? $location->id : null,
            'memory' => 1024,
            'disk' => 10240,
            'cpu' => 100,
            'swap' => 0,
            'io' => 500,
            'startup' => $egg->startup,
            'image' => $egg->docker_image,
            'environment' => [],
            'allocation_id' => $allocation ? $allocation->id : null,
            'start_on_completion' => true,
        ];

        $server = $this->creationService->handle($data);

        $this->alert->success('サーバーがテンプレートから作成されました')->flash();

        return redirect('/admin/servers/view/' . $server->id);
    }
}
