<?php

namespace DigitalOceanDropletBundle\Controller\Admin;

use DigitalOceanDropletBundle\Entity\Droplet;
use DigitalOceanDropletBundle\Service\DropletActionService;
use DigitalOceanDropletBundle\Service\DropletService;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AdminCrud(routePath: '/digital-ocean/droplet', routeName: 'digital_ocean_droplet')]
class DropletCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly DropletService $dropletService,
        private readonly DropletActionService $dropletActionService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Droplet::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('DigitalOcean虚拟机')
            ->setEntityLabelInPlural('DigitalOcean虚拟机')
            ->setPageTitle('index', 'DigitalOcean虚拟机列表')
            ->setPageTitle('detail', 'DigitalOcean虚拟机详情')
            ->setPageTitle('new', '创建DigitalOcean虚拟机')
            ->setPageTitle('edit', '编辑DigitalOcean虚拟机')
            ->setHelp('index', '管理DigitalOcean虚拟机，可以查看虚拟机信息、执行操作和同步数据')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'dropletId', 'status', 'region'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm();

        yield TextField::new('dropletId', 'Droplet ID')
            ->setMaxLength(9999)
            ->setHelp('DigitalOcean平台的虚拟机ID');

        yield TextField::new('name', '虚拟机名称')
            ->setMaxLength(100)
            ->setRequired(true)
            ->setHelp('虚拟机的显示名称');

        yield TextField::new('status', '状态')
            ->setMaxLength(50)
            ->formatValue(function ($value) {
                return match ($value) {
                    'new' => '新建中',
                    'active' => '运行中',
                    'off' => '已关闭',
                    'archive' => '已归档',
                    default => $value ?? '未知',
                };
            })
            ->setHelp('虚拟机当前状态');

        yield TextField::new('region', '区域')
            ->setMaxLength(50)
            ->setHelp('虚拟机所在的数据中心区域');

        yield TextField::new('memory', '内存')
            ->setMaxLength(50)
            ->formatValue(function ($value) {
                return $value ? $value . ' MB' : '';
            })
            ->setHelp('虚拟机内存大小');

        yield TextField::new('vcpus', 'CPU核心数')
            ->setMaxLength(50)
            ->formatValue(function ($value) {
                return $value ? $value . ' 核' : '';
            })
            ->setHelp('虚拟机CPU核心数量');

        yield TextField::new('disk', '磁盘空间')
            ->setMaxLength(50)
            ->formatValue(function ($value) {
                return $value ? $value . ' GB' : '';
            })
            ->setHelp('虚拟机磁盘空间大小');

        yield TextField::new('imageId', '镜像ID')
            ->setMaxLength(100)
            ->hideOnIndex()
            ->setHelp('使用的系统镜像ID');

        yield TextField::new('imageName', '镜像名称')
            ->setMaxLength(100)
            ->setHelp('使用的系统镜像名称');

        yield ArrayField::new('networks', '网络信息')
            ->hideOnForm()
            ->hideOnIndex()
            ->setHelp('虚拟机的网络配置信息');

        yield ArrayField::new('tags', '标签')
            ->hideOnForm()
            ->hideOnIndex()
            ->setHelp('虚拟机的标签信息');

        yield ArrayField::new('volumeIds', '存储卷')
            ->hideOnForm()
            ->hideOnIndex()
            ->setHelp('附加的存储卷ID列表');

        yield DateTimeField::new('createdAt', 'DO创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('在DigitalOcean平台创建的时间');

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('记录创建时间');

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('记录最后更新时间');
    }

    public function configureActions(Actions $actions): Actions
    {
        // 同步操作
        $syncAction = Action::new('sync', '同步虚拟机')
            ->linkToCrudAction('syncDroplets')
            ->setCssClass('btn btn-info')
            ->setIcon('fa fa-sync')
            ->createAsGlobalAction();

        // 重启操作
        $rebootAction = Action::new('reboot', '重启')
            ->linkToCrudAction('rebootDroplet')
            ->setCssClass('btn btn-warning')
            ->setIcon('fa fa-power-off');

        // 关机操作
        $shutdownAction = Action::new('shutdown', '关机')
            ->linkToCrudAction('shutdownDroplet')
            ->setCssClass('btn btn-danger')
            ->setIcon('fa fa-stop');

        // 开机操作
        $powerOnAction = Action::new('powerOn', '开机')
            ->linkToCrudAction('powerOnDroplet')
            ->setCssClass('btn btn-success')
            ->setIcon('fa fa-play');

        // 查看操作记录
        $actionsHistoryAction = Action::new('viewActions', '操作记录')
            ->linkToCrudAction('viewDropletActions')
            ->setCssClass('btn btn-secondary')
            ->setIcon('fa fa-history');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAction)
            ->add(Crud::PAGE_INDEX, $rebootAction)
            ->add(Crud::PAGE_INDEX, $shutdownAction)
            ->add(Crud::PAGE_INDEX, $powerOnAction)
            ->add(Crud::PAGE_INDEX, $actionsHistoryAction)
            ->add(Crud::PAGE_DETAIL, $rebootAction)
            ->add(Crud::PAGE_DETAIL, $shutdownAction)
            ->add(Crud::PAGE_DETAIL, $powerOnAction)
            ->add(Crud::PAGE_DETAIL, $actionsHistoryAction)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, 'reboot', 'shutdown', 'powerOn', 'viewActions', Action::EDIT, Action::DELETE])
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '虚拟机名称'))
            ->add(TextFilter::new('dropletId', 'Droplet ID'))
            ->add(TextFilter::new('status', '状态'))
            ->add(TextFilter::new('region', '区域'))
            ->add(TextFilter::new('imageName', '镜像名称'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'));
    }

    /**
     * 同步虚拟机数据
     */
    #[AdminAction('sync', 'sync_droplets')]
    public function syncDroplets(AdminContext $context, Request $request): Response
    {
        try {
            $droplets = $this->dropletService->syncDroplets();
            
            $this->addFlash('success', sprintf('成功同步 %d 台虚拟机', count($droplets)));
        } catch (\Exception $e) {
            $this->addFlash('danger', '同步失败: ' . $e->getMessage());
        }

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('admin', [
            'crudAction' => 'index',
            'crudControllerFqcn' => static::class,
        ]));
    }

    /**
     * 重启虚拟机
     */
    #[AdminAction('{entityId}/reboot', 'reboot_droplet')]
    public function rebootDroplet(AdminContext $context, Request $request): Response
    {
        /** @var Droplet $droplet */
        $droplet = $context->getEntity()->getInstance();

        try {
            $action = $this->dropletActionService->rebootDroplet($droplet->getDropletId());
            
            $this->addFlash('success', sprintf('虚拟机 "%s" 重启操作已提交，操作ID: %s', 
                $droplet->getName(), $action['id'] ?? 'unknown'));
        } catch (\Exception $e) {
            $this->addFlash('danger', '重启失败: ' . $e->getMessage());
        }

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('admin', [
            'crudAction' => 'detail',
            'crudControllerFqcn' => static::class,
            'entityId' => $droplet->getId(),
        ]));
    }

    /**
     * 关机虚拟机
     */
    #[AdminAction('{entityId}/shutdown', 'shutdown_droplet')]
    public function shutdownDroplet(AdminContext $context, Request $request): Response
    {
        /** @var Droplet $droplet */
        $droplet = $context->getEntity()->getInstance();

        try {
            $action = $this->dropletActionService->shutdownDroplet($droplet->getDropletId());
            
            $this->addFlash('success', sprintf('虚拟机 "%s" 关机操作已提交，操作ID: %s', 
                $droplet->getName(), $action['id'] ?? 'unknown'));
        } catch (\Exception $e) {
            $this->addFlash('danger', '关机失败: ' . $e->getMessage());
        }

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('admin', [
            'crudAction' => 'detail',
            'crudControllerFqcn' => static::class,
            'entityId' => $droplet->getId(),
        ]));
    }

    /**
     * 开机虚拟机
     */
    #[AdminAction('{entityId}/powerOn', 'power_on_droplet')]
    public function powerOnDroplet(AdminContext $context, Request $request): Response
    {
        /** @var Droplet $droplet */
        $droplet = $context->getEntity()->getInstance();

        try {
            $action = $this->dropletActionService->powerOnDroplet($droplet->getDropletId());
            
            $this->addFlash('success', sprintf('虚拟机 "%s" 开机操作已提交，操作ID: %s', 
                $droplet->getName(), $action['id'] ?? 'unknown'));
        } catch (\Exception $e) {
            $this->addFlash('danger', '开机失败: ' . $e->getMessage());
        }

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('admin', [
            'crudAction' => 'detail',
            'crudControllerFqcn' => static::class,
            'entityId' => $droplet->getId(),
        ]));
    }

    /**
     * 查看虚拟机操作记录
     */
    #[AdminAction('{entityId}/actions', 'view_droplet_actions')]
    public function viewDropletActions(AdminContext $context, Request $request): Response
    {
        /** @var Droplet $droplet */
        $droplet = $context->getEntity()->getInstance();

        try {
            $actionsData = $this->dropletActionService->listDropletActions($droplet->getDropletId(), 1, 50);
            
            return $this->render('@EasyAdmin/crud/action.html.twig', [
                'page_title' => sprintf('虚拟机 "%s" 的操作记录', $droplet->getName()),
                'content' => $this->renderView('@DigitalOceanDropletBundle/digital_ocean_droplet_admin/droplet_actions.html.twig', [
                    'droplet' => $droplet,
                    'actions' => $actionsData['actions'] ?? [],
                    'meta' => $actionsData['meta'] ?? [],
                ]),
            ]);
        } catch (\Exception $e) {
            $this->addFlash('danger', '获取操作记录失败: ' . $e->getMessage());
            
            return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('admin', [
                'crudAction' => 'detail',
                'crudControllerFqcn' => static::class,
                'entityId' => $droplet->getId(),
            ]));
        }
    }
} 