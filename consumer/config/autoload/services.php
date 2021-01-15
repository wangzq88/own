<?php


return [
    'consumers' => [
        [
            // name 需与服务提供者的 name 属性相同
            'name' => 'CentralbankService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\Rpc\CentralbankServiceInterface::class,
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
            'registry' => [
                'protocol' => 'consul',
                'address' => 'http://172.19.0.13:8500',
            ],
        ],
        [
            // name 需与服务提供者的 name 属性相同
            'name' => 'GlassActivitiesService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\Rpc\GlassActivitiesServiceInterface::class,
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
            'registry' => [
                'protocol' => 'consul',
                'address' => 'http://172.19.0.13:8500',
            ],
        ],
        [
            // name 需与服务提供者的 name 属性相同
            'name' => 'FiscalRevExpService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\Rpc\FiscalRevExpServiceInterface::class,
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
            'registry' => [
                'protocol' => 'consul',
                'address' => 'http://172.19.0.13:8500',
            ],
        ],
        [
            // name 需与服务提供者的 name 属性相同
            'name' => 'ImportExportService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\Rpc\ImportExportServiceInterface::class,
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
            'registry' => [
                'protocol' => 'consul',
                'address' => 'http://172.19.0.13:8500',
            ],
        ],
        [
            // name 需与服务提供者的 name 属性相同
            'name' => 'AssetInvestmentService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\Rpc\AssetInvestmentServiceInterface::class,
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
            'registry' => [
                'protocol' => 'consul',
                'address' => 'http://172.19.0.13:8500',
            ],
        ],
        [
            // name 需与服务提供者的 name 属性相同
            'name' => 'IndustrialProfitService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\Rpc\IndustrialProfitServiceInterface::class,
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
            'registry' => [
                'protocol' => 'consul',
                'address' => 'http://172.19.0.13:8500',
            ],
        ],
        [
            // name 需与服务提供者的 name 属性相同
            'name' => 'IncomeExpenditureService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\Rpc\IncomeExpenditureServiceInterface::class,
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
            'registry' => [
                'protocol' => 'consul',
                'address' => 'http://172.19.0.13:8500',
            ],
        ],
    ],
];