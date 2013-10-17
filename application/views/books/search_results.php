<div class="container">
    <div class="hero-unit" >
        <h3><?php echo $Mensaje; ?> </h3>
        <div>
            <?php
            $i =0;
            foreach ($books as $books_item):?>
                <div class="book_main">
                    <div>
                    <h5><?php $i +=1;
                    echo $books_item['title'];?></h5>
                    </div>
                    <?php echo "Autor: ".$books_item['author']. "<br />";
                    echo "Precio: ".$books_item['price']. "<br /><br />";
                    
                    $image_properties = array(
                    'src' => '../assets/img/libros/'.$books_item['image'].'',
                    'alt' => '',
                    'class' => '',
                    'width' => '100',
                    'height' => '100',
                    'title' => 'book store',
                    'rel' => 'lightbox',);
                    echo img($image_properties, TRUE);?>
                    
                    <p class="mirar_libro"><a href="<?= site_url().'books/'.$books_item['isbn'] ?>">Mirar éste libro</a></p>
                    <p><button id="cart" onclick='insertItem(value, <?php echo 'cntmsj'.$i; ?>,<?php echo 'global'.$i; ?>)' value="<?php echo $books_item['isbn']?>"></button></p>
                    
                    <p id='<?php echo 'global'.$i;?>'></p>
                    <p id='<?php echo 'cntmsj'.$i; ?>'></p>   
                </div>
            <?php endforeach ?>
            <br style="clear:both"; />
        </div>
    </div>
</div>