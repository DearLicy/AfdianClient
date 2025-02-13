<?php

/**
 * 爱发电SDK 2025/2/14
 * author : 李初一
 * QQ : 82719519
 * url : www.vxras.com
 * Class AfdianClient
 * 
 * 字段说明：
 * 
 * 订单：
 * - total_count: 赞助者总数
 * - total_page: 页数，默认每页50条，请求时，传 page ，curr_page < total_page则可继续请求
 * - out_trade_no: 订单号
 * - custom_order_id: 自定义信息
 * - user_id: 下单用户ID
 * - plan_id: 方案ID，如自选，则为空
 * - title: 订单描述
 * - month: 赞助月份
 * - total_amount: 真实付款金额，如有兑换码，则为0.00
 * - show_amount: 显示金额，如有折扣则为折扣前金额
 * - status: 2 为交易成功。目前仅会推送此类型
 * - remark: 订单留言
 * - redeem_id: 兑换码ID
 * - product_type: 0表示常规方案 1表示售卖方案
 * - discount: 折扣
 * - sku_detail: 如果为售卖类型，以数组形式表示具体型号
 * - address_person: 收件人
 * - address_phone: 收件人电话
 * - address_address: 收件人地址
 * 
 * 赞助者：
 * - total_count: 赞助者总数
 * - total_page: 页数，默认每页20条，请求时，传 page ，curr_page < total_page则可继续请求
 * - sponsor_plans: []数组类型，具体节点为多个赞助方案。
 * - current_plan: 当前赞助方案，如果节点仅有 name:"", 不包含其它内容时，表示无方案
 * - all_sum_amount: 累计赞助金额，此处为折扣前金额。如有兑换码，则此处为虚拟金额，回比实际提现的多
 * - create_time: int 秒级时间戳，表示成为赞助者的时间，即首次赞助时间
 * - last_pay_time: int 秒级时间戳，最近一次赞助时间
 * - user 节点表示用户属性
 *   - user_id: 用户唯一ID
 *   - name: 昵称，非唯一，可重复
 *   - avatar: 头像
 * 
 * 主要方法：
 * - queryOrderByOutTradeNo($out_trade_no): 根据订单号查询特定订单信息。
 * - getAllOrders(): 获取所有订单（通过处理分页）。
 * - getSponsors($page = 1, $per_page = 20): 查询赞助者信息。
 * - generateSign($params): 生成签名，用于API请求的安全验证。
 * - sendRequest($url, $payload): 发送POST请求到爱发电API并返回响应结果。
 */

class AfdianClient {
    private $user_id;
    private $token;
    private $base_url = "https://afdian.com/api/open";

    /**
     * 构造函数，初始化时需要传入用户ID和API Token。
     *
     * @param string $user_id 用户ID
     * @param string $token API Token
     */
    public function __construct($user_id, $token) {
        $this->user_id = $user_id;
        $this->token = $token;
    }

    /**
     * 生成签名，用于API请求的安全验证。
     *
     * @param array $params 请求参数
     * @return array 包含签名和时间戳
     */
    private function generateSign($params) {
        // 获取当前时间戳
        $ts = time();
        // 将参数转换为JSON字符串
        $paramsStr = json_encode($params);
        // 按照规则拼接字符串并计算MD5签名
        $signStr = "{$this->token}params{$paramsStr}ts{$ts}user_id{$this->user_id}";
        return [
            'sign' => md5($signStr),
            'ts' => $ts
        ];
    }

    /**
     * 根据订单号查询特定订单信息。
     *
     * @param string $out_trade_no 订单号
     * @return array API响应结果
     */
    public function queryOrderByOutTradeNo($out_trade_no) {
        $url = "{$this->base_url}/query-order";
        // 设置查询参数
        $params = ['out_trade_no' => $out_trade_no];
        // 生成签名和时间戳
        $signData = $this->generateSign($params);

        // 准备POST请求的数据
        $payload = [
            'user_id' => $this->user_id,
            'params' => json_encode($params),
            'ts' => $signData['ts'],
            'sign' => $signData['sign']
        ];

        // 发送请求并返回结果
        return $this->sendRequest($url, $payload);
    }

    /**
     * 获取所有订单（通过处理分页）。
     *
     * @return array 所有订单数据
     */
    public function getAllOrders() {
        $allOrders = [];
        $page = 1;

        while (true) {
            // 获取当前页的订单数据
            $orders = $this->getOrdersPage($page);
            if (empty($orders['data']['list'])) {
                break;
            }
            // 合并当前页的数据到总数据中
            $allOrders = array_merge($allOrders, $orders['data']['list']);
            $page++;
        }

        return $allOrders;
    }

    /**
     * 获取指定页面的订单信息。
     *
     * @param int $page 页码，默认第一页
     * @param int $per_page 每页条数，默认50条
     * @return array 当前页的订单数据
     */
    private function getOrdersPage($page = 1, $per_page = 50) {
        $url = "{$this->base_url}/query-order";
        // 设置查询参数
        $params = ['page' => $page, 'per_page' => $per_page];
        // 生成签名和时间戳
        $signData = $this->generateSign($params);

        // 准备POST请求的数据
        $payload = [
            'user_id' => $this->user_id,
            'params' => json_encode($params),
            'ts' => $signData['ts'],
            'sign' => $signData['sign']
        ];

        // 发送请求并返回结果
        return $this->sendRequest($url, $payload);
    }

    /**
     * 查询赞助者信息。
     *
     * @param int $page 页码，默认第一页
     * @param int $per_page 每页条数，默认20条
     * @return array 赞助者数据
     */
    public function getSponsors($page = 1, $per_page = 20) {
        $url = "{$this->base_url}/query-sponsor";
        // 设置查询参数
        $params = ['page' => $page, 'per_page' => $per_page];
        // 生成签名和时间戳
        $signData = $this->generateSign($params);

        // 准备POST请求的数据
        $payload = [
            'user_id' => $this->user_id,
            'params' => json_encode($params),
            'ts' => $signData['ts'],
            'sign' => $signData['sign']
        ];

        // 发送请求并返回结果
        return $this->sendRequest($url, $payload);
    }

    /**
     * 发送POST请求到爱发电API并返回响应结果。
     *
     * @param string $url 请求URL
     * @param array $payload 请求数据
     * @return array 响应结果
     */
    private function sendRequest($url, $payload) {
        // 初始化cURL会话
        $ch = curl_init($url);
        // 设置选项，包括返回结果而不是直接输出、POST请求、POST数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

        // 执行请求并获取响应
        $response = curl_exec($ch);
        // 关闭cURL资源，并释放系统资源
        curl_close($ch);

        // 返回解析后的JSON响应
        return json_decode($response, true);
    }
}

// 示例使用
$client = new AfdianClient('your_user_id', 'your_token');

// 根据订单号查询订单并以JSON格式输出
$result = $client->queryOrderByOutTradeNo('OrderID');
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// 获取所有订单并以JSON格式输出
$result = $client->getAllOrders();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// 获取赞助者信息并以JSON格式输出
$result = $client->getSponsors();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
