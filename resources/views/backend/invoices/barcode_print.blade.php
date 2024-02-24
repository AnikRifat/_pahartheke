<html>
    <head>
        <title>Print Barcodes</title>
        <style type="text/css" media='all'>
            *{
                box-sizing:border-box;
            }
            @page{
                margin: 0mm;
                size: auto;
            }
            body{
                margin: 0px;
            }
            .container{
                position: relative;
                max-width: 100%;
                width: 100%;
                padding:0px 10px 0 20px;
                page-break-before: always;
            }
            .code{
                text-align: center;
            }
            .text{
                position:absolute;

            }
            .code img{
                width: 100%;
                display:block;
            }
            .code p{
                font-size: 75%;
                margin:0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="code">
                @php echo'<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($barcode, 'C39') . '" />' @endphp
                <div class="text">
                    <p></p>
                </div>
            </div>
        </div>

        @if(isset($printthis))
        <script type="text/javascript">
            try { this.print(); } catch (e) { window.onload = window.print; }
            window.onbeforeprint = function() {
                setTimeout(function(){
                    window.close();
                }, 1500);
            }
        </script>
        @endif
    </body>
</html>
