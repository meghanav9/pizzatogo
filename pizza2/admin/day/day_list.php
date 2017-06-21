<?php include '../../view/header.php'; ?>
<main>
    <section>
        <h1>Today is day <?php echo $current_day; ?></h1>
        <form action="index.php" method="post">
            <input type="hidden" name="action" value="change_to_nextday">
            <input type="submit" value="Change To day <?php echo $current_day + 1; ?>" />
        </form>

        <form  action="index.php" method="post">
            <input type="hidden" name="action" value="initial_db">           
            <input type="submit" value="Initialize DB (making day = 1)" />
            <br>
        </form>
        <br>
        <h2>Today's Orders</h2>
        <?php if (count($todays_orders) > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Room No</th>
                    <th>Status</th>
                </tr>

                <?php foreach ($todays_orders as $todays_order) : ?>
                    <tr>
                        <td><?php echo $todays_order['id']; ?> </td>
                        <td><?php echo $todays_order['room_number']; ?> </td>  
                        <td><?php
                            if ($todays_order['status'] == 2) {
                                echo 'Baked';
                            } elseif ($todays_order['status'] == 1) {
                                echo 'Preparing';
                            } elseif ($todays_order['status'] == 3) {
                                echo 'Finished';
                            }
                            ?> </td>

                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No Orders Today </p>
        <?php endif; ?>
             <h2>Supplies On Order</h2> 
             <?php foreach ($supply as $s_order) : ?>
             <p> Order  <?php echo $s_order['orderid']; ?> :
                floor  <?php echo $s_order['flour_qty']; ?> 
                cheese <?php echo $s_order['cheese_qty']; ?> </p>
            <?php endforeach; ?>
             
            <h2>Current Inventory</h2>
                 <?php foreach ($cur_inventory as $curIn) : ?>
                 <?php echo $curIn['productname']; ?>
                 <?php echo $curIn['quantity']; ?></br>  
            <?php endforeach; ?>
                                
        </section>

</main>
<?php include '../../view/footer.php'; ?>