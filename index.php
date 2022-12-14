<?php

declare(strict_types = 1);

main();

function main(): void
{
    $customers = getAllCustomers();
    $orders = getAllOrders();

    $ordersPairedCustomers = [];

    foreach ($orders as $order) {
        $customerId = $order['customer_id'];

        if (!isset($ordersPairedCustomers[$customerId])) {
            $ordersPairedCustomers[$customerId] = [];
        }

        $ordersPairedCustomers[$customerId][] = $order;
    }

    foreach ($customers as $i => $customer) {
        if ($customer['active'] === false) {
            unset($customers[$i]);
            continue;
        }

        $customerId = $customer['id'];

        $customerTotalProfit = 0;
        $customerAllOrders = $ordersPairedCustomers[$customerId];

        if (!$customerAllOrders) {
            continue;
        }

        foreach ($customerAllOrders as $customerOrder) {
            if ($customerOrder['paid_at'] === null) {
                continue;
            }

            foreach ($customerOrder['items'] as $item) {
                $customerTotalProfit += $item['price'];
            }
        }

        $customers[$i]['total_profit'] = $customerTotalProfit;
    }

    usort($customers, 'compareCustomerTotalProfit');

    // Sort the ACTIVE customers from the one who bought the most from us (paid orders) to the one who bought the least
    // Customer ID, Customer email, total profit value
    foreach ($customers as $customer) {
        $row = [
            $customer['id'],
            $customer['email'],
            $customer['total_profit'],
        ];
        echo implode(';', $row) . PHP_EOL . '<br>';
    }
}

// HELPERS
/**
 * @return array<mixed>
 */
function getAllCustomers(): array
{
    $rawData = file_get_contents(__DIR__ . '/z_customers.json');

    if (!$rawData) {
        throw new Exception('File not found');
    }

    return json_decode($rawData, true);
}

/**
 * @return array<mixed>
 */
function getAllOrders(): array
{
    $rawData = file_get_contents(__DIR__ . '/z_orders.json');

    if (!$rawData) {
        throw new Exception('File not found');
    }

    return json_decode($rawData, true);
}


function compareCustomerTotalProfit($customer1, $customer2): int
{
    if ($customer1['total_profit'] == $customer2['total_profit']) {
        return 0;
    }

    return ($customer1['total_profit'] > $customer2['total_profit']) ? -1 : 1; //DESC
}