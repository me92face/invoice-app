@extends('layouts.main')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Create Invoice</div>
                <div class="card-body">
                    <table id='invoice_print' style="display: none;">
                        <tr>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Unit Price ($)</th>
                            <th>Tax %</th>
                            <th>Net Amount</th>
                            <th>Gross Amount (with tax)</th>
                        </tr>
                    </table>
                    <table id='invoice_body'>
                        <tr>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Unit Price ($)</th>
                            <th>Tax %</th>
                            <th>Net Amount</th>
                            <th>Gross Amount (with tax)</th>
                        </tr>
                        <tr>
                            <td>
                                <input type='text' name='product_name' data-id='1' id='1_product_name'>
                            </td>
                            <td>
                                <input type='number' name='qty' onkeyup='calculateRow($(this).attr("data-id"))'
                                    data-id='1' id='1_qty'>
                            </td>
                            <td>
                                <input type='number' name='unit_price' onkeyup='calculateRow($(this).attr("data-id"))'
                                    data-id='1' id='1_unit_price'>
                            </td>
                            <td>
                                <select name="tax_percent" data-id='1' onchange='calculateRow($(this).attr("data-id"))'
                                    id="1_tax_percent">
                                    <option value="0">0%</option>
                                    <option value="1">1%</option>
                                    <option value="5">5%</option>
                                    <option value="10">10%</option>
                                </select>
                            </td>
                            <td>
                                <input type='number' name='net_amount' data-id='1' id='1_net_amount' disabled>
                            </td>
                            <td>
                                <input type='number' name='gross_amount' data-id='1' id='1_gross_amount' disabled>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">Total Net Amount</td>
                            <td id='total_net_amount'>0</td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">
                                <input type="radio" id="discount" name="discount_type" onchange='calculateRow("1")' value="percent" checked>
                                <label for="discount">Discount Percentage</label><br>
                                <input type="radio" id="discount" name="discount_type" onchange='calculateRow("1")' value="amount">
                                <label for="discount">Discount Amount</label><br>
                            </td>
                            <td><input type="number" name='discount' onkeyup='calculateRow("1")' id='discount'
                                    value="0"></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">Total Gross Amount</td>
                            <td id='total_gross_amount'>0</td>
                        </tr>
                    </table>
                    <div class="row alert">
                        <button class="btn btn-primary" id='add_new_field'><i class="fa-solid fa-plus"></i> Add New</button>
                    </div>
                    <div class="row alert">
                        <button class="btn btn-success" id='print' onclick="print()"> Genearate Invoice</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let count = 2;

        function calculateRow(id) {
            let qty = $(`#${id}_qty`).val();
            let unit_price = $(`#${id}_unit_price`).val();
            let tax_percent = $(`#${id}_tax_percent`).find(":selected").val();
            let net_amount = qty * unit_price;
            let gross_amount = net_amount + (tax_percent / 100);
            $(`#${id}_net_amount`).val(net_amount)
            $(`#${id}_gross_amount`).val(gross_amount)

            let total_net_amount = 0;
            let total_gross_amount = 0;
            let discount_type = $('input[name=discount_type]:checked').val()
            console.log(discount_type)
            let discount = $("input[name=discount]").val();
            for (let i = 1; i < count; i++) {
                total_net_amount += parseFloat($(`#${i}_net_amount`).val());
                total_gross_amount += parseFloat($(`#${i}_gross_amount`).val());
            }
            if (discount_type == 'percent') {
                total_gross_amount = total_net_amount - ((discount/100)*total_net_amount);
            } else {
                total_gross_amount = +total_net_amount - discount;
            }
            $(`#total_net_amount`).html(total_net_amount)
            $(`#total_gross_amount`).html(total_gross_amount)
        }

        $(document).ready(function() {
            @if ($message = Session::get('success'))
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ $message }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif

            $("#add_new_field").click(
                function() {
                    let f1 =
                        `<input type='text' name='product_name' data-id='${count}' id='${count}_product_name'>`;
                    let f2 =
                        `<input type='text' name='qty' onkeyup='calculateRow($(this).attr("data-id"))' data-id='${count}' id='${count}_qty'>`;
                    let f3 =
                        `<input type='text' name='unit_price' onkeyup='calculateRow($(this).attr("data-id"))' data-id='${count}' id='${count}_unit_price'>`;
                    let f4 = `<select name="tax_percent" onchange="calculateRow($(this).attr('data-id'))" id="${count}_tax_percent">
                        <option value="0">0%</option>
                        <option value="1">1%</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                    </select>`;
                    let f5 =
                        `<input type='text' name='net_amount' data-id='${count}' id='${count}_net_amount' disabled>`;
                    let f6 =
                        `<input type='text' name='gross_amount' data-id='${count}' id='${count}_gross_amount' disabled>`;
                    var table = document.getElementById("invoice_body");
                    var row = table.insertRow(count);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    var cell6 = row.insertCell(5);
                    cell1.innerHTML = f1;
                    cell2.innerHTML = f2;
                    cell3.innerHTML = f3;
                    cell4.innerHTML = f4;
                    cell5.innerHTML = f5;
                    cell6.innerHTML = f6;
                    count++;
                }
            )
        });

        function print() {
            for (let i = 1; i < count; i++) {
                var table = document.getElementById("invoice_print");
                var row = table.insertRow(i);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                cell1.innerHTML = $(`#${i}_product_name`).val();
                cell2.innerHTML = $(`#${i}_qty`).val();
                cell3.innerHTML = $(`#${i}_unit_price`).val();
                cell4.innerHTML = $(`#${i}_tax_percent`).find(":selected").val();
                cell5.innerHTML = $(`#${i}_net_amount`).val();
                cell6.innerHTML = $(`#${i}_gross_amount`).val();
            }
            $('#invoice_print').show();
           
            var divToPrint = document.getElementById('invoice_print');
            newWin = window.open("");
            newWin.document.write(divToPrint.outerHTML);
            newWin.print();
            newWin.close();
        }
    </script>
@endpush

@push('css')
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
@endpush
