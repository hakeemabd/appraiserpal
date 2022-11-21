<?php
/**
 * Created by PhpStorm.
 * User: dez
 * Date: 03.02.16
 * Time: 19:57
 */

namespace App\Http\Controllers;


use App\Models\Attachment;
use App\Models\Order;
use App\Repositories\AttachmentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use yajra\Datatables\Datatables;
use Storage;
use ZipArchive;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class DocumentsController extends Controller
{
    protected $attachmentRepository;

    public function __construct(AttachmentRepository $attachmentRepository, OrderRepository $orderRepository, UserRepository $userRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    public function getFiles($orderId)
    {
        $userId = $this->orderRepository->find($orderId)->getUserid();
        $filesCollection = $this->attachmentRepository->getOrderFiles($orderId, true);
        $filesCollection = $filesCollection->filter(function ($item) use ($userId) {
            return $item->getUserId() == $userId;
        });

        $dataTable = Datatables::of($filesCollection)
            ->addRowData('actions', function ($result) {
                if(Sentinel::check()->inRole('administrator') || Sentinel::check()->inRole('sub-admin')) {
                    return [
                        'remove' => [
                            'link' => route(env('SYSTEM_SCOPE') . ':attachment.destroy', ['attachment' => $result->id]),
                            'confirm' => true,
                            'confirm-type' => 'remove',
                            'method' => 'DELETE'
                        ]
                    ];
                } else {
                    return [];
                }
            })
            ->editColumn('type', function ($result) {
                if($result->type == Attachment::TYPE_COMPARABLE) {
                    $label = collect(json_decode($result->label, true))
                        ->forget(['zip', 'city', 'state'])->toArray();

                    $output = implode('/', array_map(
                        function ($v, $k) { return sprintf("%s:%s", $k, $v); },
                        $label,
                        array_keys($label)
                    ));

                    return $result->type . '/' . $output;
                }
                return $result->type;
            })
            ->editColumn('name', function ($result) {
                return '<a class="text-primary" href="' . $result->path . '" target="_blank">' . $result->name . '</a>';
            })
            ->make(true);

            return $dataTable;
    }

    //gets files uploaded for this order by workers and admin
    public function getUploadedFiles($orderId)
    {
        $admins = $this->userRepository->admins()->get();
        $workers = $this->userRepository->workers()->get();

        $adminsAndWorkers = $admins->merge($workers);

        $filesCollection = $this->attachmentRepository->getOrderFiles($orderId, true);

        $filesCollection = $filesCollection->filter(function ($item) use ($adminsAndWorkers) {
            return in_array($item->getUserid(), $adminsAndWorkers->pluck('id')->toArray());
        });

        return Datatables::of($filesCollection)
            ->addRowData('actions', function ($result) {
                $actions = [];

                $type = ($result->final) ? 'unmark as final' : 'mark as final';

                $actions[$type] = [
                    'link' => route(env('SYSTEM_SCOPE') . ':attachment.mark', ['attachment' => $result->id, 'final' => (int) !$result->final]),
                    'method' => 'GET'
                ];

                $actions['remove'] = [
                        'link' => route(env('SYSTEM_SCOPE') . ':attachment.destroy', ['attachment' => $result->id]),
                        'confirm' => true,
                        'confirm-type' => 'remove',
                        'method' => 'DELETE'
                ];

                return $actions;
            })
            ->addColumn('col_file_label', function ($result) {
                return $result->getlabel();
            }, false)
            ->addColumn('col_editor_name', function ($result) use ($adminsAndWorkers) {
                $user = $adminsAndWorkers->filter(function($item) use($result) {
                    return $item->id == $result->getUserId();
                })->first();

                $name = $user->getFullNameAttribute();
                if(env('SYSTEM_SCOPE') != 'admin') {
                    $name = 'ID' . $user->getId();
                    if(Sentinel::findById($user->getId())->inRole('administrator') || Sentinel::findById($user->getId())->inRole('sub-admin')) {
                        return 'Admin';
                    }
                }
                return $name;

            }, false)
            ->addColumn('col_file_name', function ($result) {
                return '<a class="text-primary" href="' . $result->path . '" target="_blank">' . $result->name . '</a>';
            }, false)
            ->addColumn('col_file_is_final', function ($result) {
                return $result->final ? 'Yes' : 'No';
            }, false)
            ->make(true);
    }

    //gets final files uploaded for this order by workers and admin
    public function getFinalFiles($orderId)
    {  
        $order = order::find($orderId);
        if ($order->status === order::STATUS_DONE || $order->status === order::STATUS_DELIVERED) {
            $admins = $this->userRepository->admins()->get();
            $workers = $this->userRepository->workers()->get();

            $adminsAndWorkers = $admins->merge($workers);

            $filesCollection = $this->attachmentRepository->getFinalOrderFiles($orderId, true);

            $filesCollection = $filesCollection->filter(function ($item) use ($adminsAndWorkers) {
                return in_array($item->getUserid(), $adminsAndWorkers->pluck('id')->toArray());
            });

            return Datatables::of($filesCollection)
                ->addRowData('actions', function ($result) {
                    $actions = [];

                    if ($result->approved !== 1) {
                        $actions['approve'] = [
                            'link' => route(env('SYSTEM_SCOPE') . ':attachment.approve', ['attachment' => $result->id, 'approved' => 1]),
                            'method' => 'GET'
                        ];
                    }

                    if ($result->approved === 1 || $result->approved === null) {
                        $actions['disapprove'] = [
                            'link' => route(env('SYSTEM_SCOPE') . ':attachment.approve', ['attachment' => $result->id, 'approved' => 0]),
                            'method' => 'GET'
                        ];
                    }

                    $actions['remove'] = [
                            'link' => route(env('SYSTEM_SCOPE') . ':attachment.destroy', ['attachment' => $result->id]),
                            'confirm' => true,
                            'confirm-type' => 'remove',
                            'method' => 'DELETE'
                    ];

                    return $actions;
                })
                ->addColumn('col_is_approved', function ($result) {
                    if ($result->approved === 1) {
                        return 'Yes';
                    } elseif ($result->approved === 0) {
                        return 'No';
                    }

                    return 'On Hold';
                }, false)
                ->addColumn('col_file_label', function ($result) {
                    return $result->getlabel();
                }, false)
                ->addColumn('col_editor_name', function ($result) use ($adminsAndWorkers) {
                    $user = $adminsAndWorkers->filter(function($item) use($result) {
                        return $item->id == $result->getUserId();
                    })->first();

                    $name = $user->getFullNameAttribute();
                    if(env('SYSTEM_SCOPE') != 'admin') {
                        $name = 'ID' . $user->getId();
                        if(Sentinel::findById($user->getId())->inRole('administrator') || Sentinel::findById($user->getId())->inRole('sub-admin')) {
                            return 'Admin';
                        }
                    }
                    return $name;

                }, false)
                ->addColumn('col_file_name', function ($result) {
                    return '<a class="text-primary" href="' . $result->path . '" target="_blank">' . $result->name . '</a>';
                }, false)
                ->make(true);
        } else {
            return Datatables::of($this->attachmentRepository->getFinalOrderFiles(0, true))
                ->addRowData('actions', function ($result) {
                    $actions = []; 

                    return $actions;
                })
                ->addColumn('col_file_label', function ($result) {
                    return '';
                }, false)
                ->addColumn('col_editor_name', function ($result) {
                    return '';

                }, false)
                ->addColumn('col_file_name', function ($result) {
                    return '';
                }, false)
                ->make(true);
        }
    }

    //download all approved deliverables
    public function downloadDeliverables($orderId)
    {  
        try {
            $order = order::find($orderId);
            if ($order->status === order::STATUS_DONE || $order->status === order::STATUS_DELIVERED) {
                $admins = $this->userRepository->admins()->get();
                $workers = $this->userRepository->workers()->get();

                $adminsAndWorkers = $admins->merge($workers);

                $filesCollection = $this->attachmentRepository->getFinalOrderFiles($orderId, true);

                $filesCollection = $filesCollection->filter(function ($item) use ($adminsAndWorkers) {
                    return in_array($item->getUserid(), $adminsAndWorkers->pluck('id')->toArray());
                });
            }
            $uuid = uniqid();
            mkdir('delivers-'.$uuid);
            foreach ($filesCollection as $file) {
                file_put_contents('delivers-'.$uuid.'/'.$file->getlabel().'-'.$file->name, fopen($file->path, 'r'));
            }

            function add_zip($dir, $zip) { 
              if (is_dir($dir)) {
                if ($da = opendir($dir)) {
                  while (($file = readdir($da)) !== false) {
                    if (is_dir($dir .'/'. $file) && $file != "." && $file != "..") {
                      agregar_zip($dir .'/'. $file . "/", $zip);
                    } elseif (is_file($dir .'/'. $file) && $file != "." && $file != "..") {
                      $zip->addFile($dir .'/'. $file, $dir .'/'. $file);
                    }
                  }
                  closedir($da);
                }
              }
            };

            function removeDirectory($path)
            {
                $path = rtrim( strval( $path ), '/' ) ;
                
                $d = dir( $path );
                
                if( ! $d )
                    return false;
                
                while ( false !== ($current = $d->read()) )
                {
                    if( $current === '.' || $current === '..')
                        continue;
                    
                    $file = $d->path . '/' . $current;
                    
                    if( is_dir($file) )
                        removeDirectory($file);
                    
                    if( is_file($file) )
                        unlink($file);
                }
                
                rmdir( $d->path );
                $d->close();
                return true;
            };

            $zip = new ZipArchive();
            $dir = 'delivers-'.$uuid;

            $finalRoute = 'delivers-'.$uuid;

            if(!file_exists($finalRoute)){
              mkdir($finalRoute);
            }

            $fileZip = "deliverables_order(".$order->id.").zip";

            if ($zip->open($fileZip, ZIPARCHIVE::CREATE) === true) {
              add_zip($dir, $zip);
              $zip->close();
              removeDirectory($dir);
              if (file_exists($fileZip)) {
                return response()->json([
                    'success' => true,
                    'message' => $fileZip,
                ], SymfonyResponse::HTTP_OK);
              } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unexpected error, try again later',
                ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
              }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
