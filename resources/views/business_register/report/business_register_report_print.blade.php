<!DOCTYPE html>
<html>
    <head>
        <title>Register Report | {{ $store }} | {{ $date }}</title>
    </head>
    <body>

        <div class='mb-1rem'>
            <table>
                <tr>
                    <td><h2>REGISTER REPORT</h2></td>
                </tr>
            </table>

            <table class='w-100 bordered-table'>
                <tr>
                    <th class='w-50 left'>
                        Store
                    </th>
                    <td class='w-50'>
                        {{ $store }}
                    </td>
                </tr>
                <tr>
                    <th class='w-50 left'>
                        Billing Counter
                    </th>
                    <td class='w-50'>
                        {{ $data['billing_counter']['billing_counter_code'] }} - {{ $data['billing_counter']['counter_name'] }}
                    </td>
                </tr>
                <tr>
                    <th class='w-50 left'>
                        Opened By
                    </th>
                    <td class='w-50'>
                        {{ $data['user']['fullname'] }}
                    </td>
                </tr>
                <tr>
                    <th class='w-50 left'>
                        Opened On
                    </th>
                    <td class='w-50'>
                        {{ $data['opening_date_label'] }}
                    </td>
                </tr>
                <tr>
                    <th class='w-50 left'>
                        Closed On
                    </th>
                    <td class='w-50'>
                        {{ ($data['closing_date_label'])?$data['closing_date_label']:'-' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class='mb-1rem'>
            <table class='w-100 bordered-table'>
                <tr>
                    <th class=''>
                        
                    </th>
                    <th class=''>
                        Amount ({{ $currency }})
                    </th>
                    <th class=''>
                        Count
                    </th>
                </tr>
                <tr>
                    <td class=''>
                        Opening Amount
                    </td>
                    <td class=''>
                        {{ $data['opening_amount'] }}
                    </td>
                    <td class=''>
                        -
                    </td>
                </tr>
                <tr>
                    <td class=''>
                        Closing Amount
                    </td>
                    <td class=''>
                        {{ $data['closing_amount'] }}
                    </td>
                    <td class=''>
                        -
                    </td>
                </tr>
                <tr>
                    <td class=''>
                        Credit Card Slips
                    </td>
                    <td class=''>
                        -
                    </td>
                    <td class=''>
                        {{ $data['credit_card_slips'] }}
                    </td>
                </tr>
                <tr>
                    <td class=''>
                        Cheques
                    </td>
                    <td class=''>
                        -
                    </td>
                    <td class=''>
                        {{ $data['cheques'] }}
                    </td>
                </tr>
                @foreach ($data['payment_method_data'] as $payment_method)
                <tr>
                    <td class=''>
                        {{ $payment_method['payment_method'] }}
                    </td>
                    <td class=''>
                        {{ $payment_method['value'] }}
                    </td>
                    <td class=''>
                        {{ $payment_method['order_count'] }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td class=''>
                        Order Grand Total
                    </td>
                    <td class=''>
                        {{ $data['order_data']['order_value'] }}
                    </td>
                    <td class=''>
                        {{ $data['order_data']['order_count'] }}
                    </td>
                </tr>
            </table>
        </div>

        <div class='center'>
            <div class='display-block'>Thank You!</div>
        </div>

    </body>
</html>