<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="WXrnYWjpRQ0sq4WtYHNk04bfM1AXerEu9Vhky7lD">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Track Cert - Order Invoice</title>
</head>

<body id="invoice-template" style="background: #fbf8f3;">
  <table align="center" cellpadding="0" cellspacing="0" width="100%" style="font-family: Helvetica , sans-serif; margin:0px auto 10px auto; vertical-align: top; background: #fff; padding: 10px;">
    <tr>
      <td>
        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:1rem;border-bottom:1px solid #f0f0f0">
          <tbody>
            <tr>
              <td valign="top" style="padding: 10px">
                <img src="{{ assets('assets/website-images/logo.jpg') }}" width="100">
              </td>

              <td valign="top" style="padding: 10px">
                <h6 style="margin: 0 0 10px 0;    text-align: right;color: #2d2f3c;font-size: 16px;font-weight: bold;padding: 0;">Tax Invoice/Bill of Supply/Cash Memo</h6>
                <p style="font-size: 14px;    text-align: right; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">(Original for Recipient)</p>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <table align="center" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0; margin-bottom: 1rem">
                <tr>
                  <td valign="top">

                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      Order Number: <b>{{ $order->order_number ?? "NA" }}</b>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td valign="top">
                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      Order Date: <b>{{ date('d M, Y H:iA', strtotime($order->created_date)) }}</b>
                    </p>
                  </td>
                </tr>
              </table>
            </td>

            <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0; margin-bottom: 1rem">
                <tr>
                  <td valign="top">

                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      Invoice No: <b>{{ "INV".date('dmyhis') }}</b>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td valign="top">
                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      Invoice Date: <b>{{ date('d M, Y H:iA') }}</b>
                    </p>
                  </td>
                </tr>
              </table>
            </td>

            <!-- <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0; margin-bottom: 1rem">
                <tr>
                  <td valign="top">
                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      PAN No: <b>NA</b>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td valign="top">
                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      GSTIN <b>NA</b>
                    </p>
                  </td>
                </tr>
              </table>
            </td> -->
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td>
        <table align="center" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0; margin-bottom: 1rem">
                <tr>
                  <td valign="top" style="padding: 10px;">
                    <h6 style="margin: 0 0 10px 0;color: #2d2f3c;font-size: 16px;font-weight: bold;padding: 0;">Purchased By :</h6>
                    <p style="font-size: 15px;font-weight: bold;color: #858796;margin:0">{{ $order->first_name ?? "NA" }} {{ $order->last_name ?? "" }}</p>
                    <p style="font-size: 14px;font-weight: normal;color: #858796;margin:0">{{ $order->email ?? "NA" }}</p>
                    <p style="font-size: 14px;font-weight: normal;color: #858796;margin:0;line-height: normal;">+1 {{ $order->phone ?? "NA" }}</p>
                  </td>
                </tr>
              </table>
            </td>

            <!-- <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0; margin-bottom: 1rem">
                <tr>
                  <td valign="top" style="padding: 10px;">
                    <h6 style="margin: 0 0 10px 0;color: #2d2f3c;font-size: 16px;font-weight: bold;padding: 0;">Billing Address :</h6>
                    <p style="font-size: 15px;font-weight: bold;color: #858796;margin:0">John Doe</p>
                    <p style="font-size: 14px;font-weight: normal;color: #858796;margin:0">Steelworkers Local # 42058</p>
                    <p style="font-size: 14px;font-weight: normal;color: #858796;margin:0;line-height: normal;">110 24th St, Niagara Falls, NY 14303, United States</p>
                  </td>
                </tr>
              </table>
            </td>

            <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0; margin-bottom: 1rem">
                <tr>
                  <td valign="top" style="padding: 10px;">
                    <h6 style="margin: 0 0 10px 0;color: #2d2f3c;font-size: 16px;font-weight: bold;padding: 0;">Shipping Address:</h6>
                    <p style="font-size: 15px;font-weight: bold;color: #858796;margin:0">John Doe</p>
                    <p style="font-size: 14px;font-weight: normal;color: #858796;margin:0">Steelworkers Local # 42058</p>
                    <p style="font-size: 14px;font-weight: normal;color: #858796;margin:0;line-height: normal;">110 24th St, Niagara Falls, NY 14303, United States</p>
                  </td>
                </tr>
              </table>
            </td> -->

          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td>
        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="font-family: Helvetica , sans-serif; margin:0px auto 10px auto;">
          <tr>
            <td>
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0">
                <thead>
                  <tr>
                    <th style="font-size: 14px;background: #f0f0f0;border: none;color: #2d2f3c;padding: 10px">S.No.</th>
                    <th style="font-size: 14px;background: #f0f0f0;border: none;color: #2d2f3c;padding: 10px">Title</th>
                    <th style="font-size: 14px;background: #f0f0f0;border: none;color: #2d2f3c;padding: 10px">Type</th>
                    @if($order->order_for==1)
                    <th style="font-size: 14px;background: #f0f0f0; border: none; color: #2d2f3c;padding: 10px">Actual Amount</th>
                    <th style="font-size: 14px;background: #f0f0f0; border: none; color: #2d2f3c;padding: 10px">Discount Amount</th>
                    @else
                    <th style="font-size: 14px;background: #f0f0f0; border: none; color: #2d2f3c;padding: 10px">Amount</th>
                    <th style="font-size: 14px;background: #f0f0f0; border: none; color: #2d2f3c;padding: 10px">Shipping Amount</th>
                    @endif
                    <th style="font-size: 14px;background: #f0f0f0; border: none; color: #2d2f3c;padding: 10px">Quantity</th>
                    <th style="font-size: 14px;background: #f0f0f0; border: none; color: #2d2f3c;padding: 10px">Total Fee Paid</th>
                  </tr>
                </thead>
                <tbody>

                  @php $amount = 0; $admin = 0; @endphp
                  @forelse($orderDetails as $key => $val)
                  <tr>
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">{{ $key+1 }}</td>
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px; text-transform: uppercase;">{{ $val->title ?? "NA" }}</td>
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px; text-transform: uppercase;">
                        @if($val->product_type == 1) Course
                        @else Product
                        @endif
                    </td>

                    @if($order->order_for==1)
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">${{ number_format((float)$val->course_fee, 2, '.', '') }}</td>
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">${{ number_format((float)($val->course_fee-$val->amount), 2, '.', '') ?? 0 }}</td>
                    @else
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">${{ number_format((float)($val->amount), 2, '.', '') ?? 0 }}</td>
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">${{ number_format((float)($val->shipping_price), 2, '.', '') ?? 0 }}</td>
                    @endif
                    
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">{{ $val->quantity ?? 1 }}</td>
                    @if($order->order_for == 1)
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">${{ number_format((float)$val->course_fee, 2, '.', '') }}</td>
                    @else
                    <td style="font-size: 14px; font-weight: 400;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">${{ number_format((float)($val->amount * $val->quantity), 2, '.', '') }}</td>
                    @endif
                  </tr>
                  @php $amount += $val->amount; $admin += $val->admin_amount;  @endphp
                  @empty
                  @endforelse

                  <tr>
                    <td colspan="6" style="font-size: 16px;font-weight: bold;border-top: 1px solid #f0f0f0;border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0;color: #858796;padding: 10px;margin: 0;"> Sub Total
                    </td>
                    @if($order->order_for == 1)
                    <td colspan="1" style="font-size: 14px; font-weight: bold;border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">
                      &nbsp;${{ number_format((float)($order->amount + $order->coupon_discount_price ?? 0), 2, '.', '') }}
                    </td>
                    @else
                    <td colspan="1" style="font-size: 14px; font-weight: bold;border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">
                      ${{ number_format((float)$order->amount, 2, '.', '') }}
                    </td>
                    @endif
                  </tr>
                  @if($order->order_for == 2)
                  <tr>
                    <td colspan="6" style="font-size: 16px;font-weight: bold;border-top: 1px solid #f0f0f0;border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0;color: #858796;padding: 10px;margin: 0;"> Shipping Fee
                    </td>
                    <td colspan="1" style="font-size: 14px; font-weight: bold;border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">+${{number_format((float)$order->delivery_charges ?? 0, 2, '.', '')}}
                    </td>
                  </tr>
                  @endif
                  <tr>
                    <td colspan="6" style="font-size: 16px;font-weight: bold;border-top: 1px solid #f0f0f0;border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0;color: #858796;padding: 10px;margin: 0;"> Tax
                    </td>
                    <td colspan="1" style="font-size: 14px; font-weight: bold;border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">+${{ number_format((float)$order->taxes ?? 0, 2, '.', '') }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6" style="font-size: 16px;font-weight: bold;border-top: 1px solid #f0f0f0;border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0;color: #858796;padding: 10px;margin: 0;"> Coupon Discount
                    </td>
                    <td colspan="1" style="font-size: 14px; font-weight: bold;border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">-${{ number_format((float)$order->coupon_discount_price ?? 0, 2, '.', '') }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6" style="font-size: 16px;font-weight: bold;border-top: 1px solid #f0f0f0;border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0;color: #858796;padding: 10px;margin: 0;"> Total
                    </td>
                    <td colspan="1" style="font-size: 14px; font-weight: bold;border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0; line-height: 1.5; color: #858796; padding: 10px;">&nbsp;${{ $order->total_amount_paid ?? 0 }}
                    </td>
                  </tr>

                  <!-- <tr>
                      <td align="right" colspan="12">
                        <p style="font-size: 16px;font-weight: bold;color: #858796;padding: 10px;margin: 0;">For Stephenie Meyer:</p>
                        <div><img src="images/Stephenie-meyer-signature.png" height="100"></div>
                        <p style="font-size: 16px;font-weight: bold;color: #858796;padding: 10px;margin: 0;">Authorized Signatory</p>
                      </td>
                    </tr> -->

                </tbody>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>



    <tr>
      <td>
        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #f0f0f0">
          <tr>
            <!-- <td valign="top">
                  <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border-right:1px solid #f0f0f0"> 
                    <tr > 
                      <td valign="top">
                        <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                          Payment Transaction ID: <b>gtozfrxPJzWv2mJf5j4P</b> 
                        </p>
                      </td>
                    </tr>
                  </table>  
                </td> -->

            <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border-right:1px solid #f0f0f0">
                <tr>
                  <td valign="top">
                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      Date & Time: <b>{{ date('d M, Y H:iA') }}</b>
                    </p>
                  </td>
                </tr>
              </table>
            </td>

            <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%" style="border-right:1px solid #f0f0f0">
                <tr>
                  <td valign="top">

                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      Invoice Value: <b>${{$order->total_amount_paid ?? 0}}</b>
                    </p>
                  </td>
                </tr>
              </table>
            </td>

            <td valign="top">
              <table align="center" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td valign="top">
                    <p style="font-size: 14px; font-weight: 400; line-height: 1.5; color: #858796; padding: 5px 10px; margin:0">
                      Mode of Payment: <b>STRIPE</b>
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    </td>
    </tr>
  </table>


</body>

</html>