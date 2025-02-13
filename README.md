# 爱发电 API SDK

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## 概述

这是一个用于与爱发电平台API进行交互的PHP SDK。该SDK提供了以下功能：

- 根据订单号查询特定订单信息
- 获取所有订单（通过处理分页）
- 查询赞助者信息

## 目录

- [安装](#安装)
  - [使用 Composer 安装](#使用-composer-安装)
  - [手动安装](#手动安装)
- [使用方法](#使用方法)
  - [初始化客户端](#初始化客户端)
  - [根据订单号查询订单](#根据订单号查询订单)
  - [获取所有订单](#获取所有订单)
  - [获取赞助者信息](#获取赞助者信息)
- [贡献](#贡献)
- [许可证](#许可证)
- [联系方式](#联系方式)

## 安装

### 使用 Composer 安装

如果你使用 Composer 来管理你的 PHP 项目，可以通过以下命令安装此 SDK：

```bash
composer require dearlicy/AfdianClient
```

## 手动安装

1. 下载或克隆此仓库到本地：
```bash
git clone https://github.com/DearLicy/AfdianClient.git
```
2. 将SDK文件夹复制到你的项目中，并确保自动加载路径正确

## 使用方法

### 初始化客户端

首先，你需要实例化`AfdianClient`类并传入你的用户ID和Token：

```php
$client = new AfdianClient('your_user_id', 'your_token');
```

### 根据订单号查询订单

你可以使用`queryOrderByOutTradeNo`方法来查询特定订单的信息：

```php
$result = $client->queryOrderByOutTradeNo('202502140412095756101541924');
header('Content-Type: application/json');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
```

### 获取所有订单

要获取所有订单（通过处理分页），可以使用`getAllOrders`方法：

```php
$result = $client->getAllOrders();
header('Content-Type: application/json');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
```

### 获取赞助者信息

使用`getSponsors`方法来查询赞助者信息：

```php
$result = $client->getSponsors();
header('Content-Type: application/json');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
```

## 联系方式

如果有任何问题或建议，请通过以下方式联系我：

* GitHub: [DearLicy](https://github.com/DearLicy)
* email: [DearLicy](mailto:DearLicy@gmail.com)
