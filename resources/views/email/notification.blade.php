<html>

<head>
    <style>
        td {
            padding-right: 20px;
            padding-left: 10px;
        }

        .container {
            width: 350px;
            height: 450px;
            background-color: #fafafa;
        }

        .btn {
            display: inline-block;
            background-color: #0C82B9;
            color: #FFFFFF;
            padding: 8px 13px;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            opacity: 0.9;
            border-radius: 5px;
        }

        .header {
            background-color: #005885;
            padding: 5px 20px;
            color: #fff;
        }

        .body {
            margin-left: 20px;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .gsmart {
            text-align: center;
            margin-top: 20px;
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3>{{ $data['subject'] }}</h3>
        </div>
        <div class="body">
            <p>Dear {{ $data['body']['user_name'] }}, <br>{{ $data['body']['message'] }}</p>
            <table style="margin: 20px 0;">
                @if ($data['type'] == 1) {{-- Request upgrade level & upload COGS --}}
                <tr>
                    <td>AMS Name</td>
                    <td>: {{ $data['body']['ams_name'] }}</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td>: {{ $data['body']['customer'] }}</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>: {{ $data['body']['ac_reg'] }}</td>
                </tr>
                <tr>
                    <td>Type</td>
                    <td>: {{ $data['body']['type'] }}</td>
                </tr>
                <tr>
                    <td>Level</td>
                    <td>: {{ $data['body']['level'] }}</td>
                </tr>
                <tr>
                    <td>Progress</td>
                    <td>: {{ $data['body']['progress'] }}%</td>
                </tr>
                <tr>
                    <td>TAT</td>
                    <td>: {{ $data['body']['tat'] }}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>: {{ $data['body']['start_date'] }}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>: {{ $data['body']['end_date'] }}</td>
                </tr>
                @elseif ($data['type'] == 2) {{-- Request hangar slot --}}
                <tr>
                    <td>AMS Name</td>
                    <td>: {{ $data['body']['ams_name'] }}</td>
                </tr>
                <tr>
                    <td>Hangar</td>
                    <td>: {{ $data['body']['hangar'] }}</td>
                </tr>
                <tr>
                    <td>Line</td>
                    <td>: {{ $data['body']['line'] }}</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>: {{ $data['body']['ac_reg'] }}</td>
                </tr>
                <tr>
                    <td>TAT</td>
                    <td>: {{ $data['body']['tat'] }}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>: {{ $data['body']['start_date'] }}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>: {{ $data['body']['end_date'] }}</td>
                </tr>
                @elseif ($data['type'] == 20) {{-- Approve hangar slot --}}
                <tr>
                    <td>CBO Name</td>
                    <td>: {{ $data['body']['cbo_name'] }}</td>
                </tr>
                <tr>
                    <td>Hangar</td>
                    <td>: {{ $data['body']['hangar'] }}</td>
                </tr>
                <tr>
                    <td>Line</td>
                    <td>: {{ $data['body']['line'] }}</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>: {{ $data['body']['ac_reg'] }}</td>
                </tr>
                <tr>
                    <td>TAT</td>
                    <td>: {{ $data['body']['tat'] }}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>: {{ $data['body']['start_date'] }}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>: {{ $data['body']['end_date'] }}</td>
                </tr>
                @elseif ($data['type'] == 3) {{-- Request reschedule --}}
                <tr>
                    <td>AMS Name</td>
                    <td>: {{ $data['body']['ams_name'] }}</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td>: {{ $data['body']['customer'] }}</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>: {{ $data['body']['ac_reg'] }}</td>
                </tr>
                <tr>
                    <td>Hangar</td>
                    <td>: From <b>{{ $data['body']['hangar'] }}</b> to <b>{{ $data['body']['new_hangar'] }}</b></td>
                </tr>
                <tr>
                    <td>Line</td>
                    <td>: From <b>{{ $data['body']['line'] }}</b> to <b>{{ $data['body']['new_line'] }}</b></td>
                </tr>
                <tr>
                    <td>TAT</td>
                    <td>: From <b>{{ $data['body']['tat'] }} days</b> to <b>{{ $data['body']['new_tat'] }} days</b></td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>: From <b>{{ $data['body']['start_date'] }}</b> to <b>{{ $data['body']['new_s_date'] }}</b></td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>: From <b>{{ $data['body']['end_date'] }}</b> to <b>{{ $data['body']['new_e_date'] }}</b></td>
                </tr>
                @elseif ($data['type'] == 30){{-- Approve reschedule --}}
                <tr>
                    <td>Customer</td>
                    <td>: {{ $data['body']['customer'] }}</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>: {{ $data['body']['ac_reg'] }}</td>
                </tr>
                <tr>
                    <td>Hangar</td>
                    <td>: {{ $data['body']['hangar'] }}</td>
                </tr>
                <tr>
                    <td>Line</td>
                    <td>: {{ $data['body']['line'] }}</td>
                </tr>
                <tr>
                    <td>TAT</td>
                    <td>: {{ $data['body']['tat'] }}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>: {{ $data['body']['start_date'] }}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>: {{ $data['body']['end_date'] }}</td>
                </tr>
                @elseif ($data['type'] == 4)
                <tr>
                    <td>AMS Name</td>
                    <td>: {{ $data['body']['ams_name'] }}</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td>: {{ $data['body']['customer'] }}</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>: {{ $data['body']['ac_reg'] }}</td>
                </tr>
                <tr>
                    <td>TAT</td>
                    <td>: {{ $data['body']['tat'] }}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>: {{ $data['body']['start_date'] }}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>: {{ $data['body']['end_date'] }}</td>
                </tr>
                <tr>
                    <td>Cancel Category</td>
                    <td>: {{ $data['body']['category'] }}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>Cancel Reason</td>
                    <td>: {{ $data['body']['reason'] }}</td>
                </tr>
                @elseif ($data['type'] == 40)
                <tr>
                    <td>Customer</td>
                    <td>: {{ $data['body']['customer'] }}</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>: {{ $data['body']['ac_reg'] }}</td>
                </tr>
                <tr>
                    <td>TAT</td>
                    <td>: {{ $data['body']['tat'] }}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>: {{ $data['body']['start_date'] }}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>: {{ $data['body']['end_date'] }}</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="gsmart">
            <a href="{{ $data['body']['link'] }}" class="btn" style="color: #fff;">Open GSMART</a>
        </div>
    </div>
</body>

</html>
