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
        $customerId = $customer['id'];

        $customerTotalProfit = 0;
        $customerAllOrders = $ordersPairedCustomers[$customerId];

        if (!$customerAllOrders) {
            continue;
        }

        foreach ($customerAllOrders as $customerOrder) {
            $customerTotalProfit += $customerOrder['items'][0]['price'];
        }

        $customers[$i]['total_profit'] = $customerTotalProfit;
    }

    // Missing array sorting method (with callback?)
    //
    //
    //
    //
    //

    // Sort the ACTIVE customers from the one who bought the most from us (paid orders) to the one who bought the least
    // Customer ID, Customer email, total profit value
    foreach ($customers as $customer) {
        $row = [
            $customer['id'],
            $customer['email'],
            $customer['total_profit'],
        ];
        echo implode(';', $row) . PHP_EOL;
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
