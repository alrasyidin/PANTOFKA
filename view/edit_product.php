<div id="centered-section min-height-400">
    <div class="info_from_controller; centered-section">
        <?php
        if(isset($_SESSION["edit_product_result"])){
            ?>
            <h3> <?= $_SESSION["edit_product_result"]?></h3>
        <?php
            unset($_SESSION["edit_product_result"]);
        }
        ?>
    </div>
    <?php
    $productDao = new \model\dao\ProductsDao();
    $sizeDao = new \model\dao\SizeDao();

    if (isset($_SESSION["user"])) {
        /* @var $user \model\User */
        $user = $_SESSION["user"];
        /* @var $product \model\Product */
     if (isset($_GET["id"]) || isset($_SESSION["product_to_edit_id"])) {
         if (isset($_GET["id"])) {
             $product_id = htmlentities($_GET["id"]);
         }
         elseif (isset($_SESSION["product_to_edit_id"])) {
             $product_id = $_SESSION["product_to_edit_id"];
             unset($_SESSION["product_to_edit_id"]);

         }
         else{
             header("location: index.php?page=unauthorized");
             die();
         }
         $product = $productDao->getProductById($product_id);
         $colors = $productDao->getColors();
         $materials = $productDao->getMaterials();
         $styles = $productDao->getStylesByParentCategory($product->getCategory());
         $sizes = $sizeDao->getSizesAndQuantities($product_id);

         if ($user->getisAdmin()) {
             ?>
             <div class="centered-section">
                 <div class="form">
                     Category: <h3><?= $product->getCategory() ?></h3>
                     <form action="handle_requests.php?target=admin&action=changeProduct" method="post" enctype="multipart/form-data">
                         Product name: <input type="text" name="product_name"
                                              value="<?= $product->getProductName() ?>" required> <br>
                         <div>
                             <img width="200px"
                                  src="<?= $product->getProductImageUrl() ?>"
                                  alt="picture of the product">
                         </div>
                         Choose another image: <input type="file" name="product_image_url" accept="image/*"><br>

                         Product price:
                         <input type="number" name="product_price"
                                value="<?= $product->getPrice() ?>" required> <br>

                         Promo percentage:
                         <input type="number" name="promo_percentage"
                                value="<?= $product->getPromoPercentage() ?>" required> <br>


                         Product style : <select id="select-style" name="product_style" required>
                             <?php
                             foreach ($styles as $style) {
                                 ?>
                                 <option value="<?= $style ?>" <?php if ($product->getStyle() === $style) { ?>
                                     selected <?php } ?>> <?= $style ?> </option>
                                 <?php
                             }
                             ?>

                         </select>

                         Product color : <select id="select-color" name="product_color" required>
                             <?php
                             foreach ($colors as $color) {
                                 ?>
                                 <option value="<?= $color ?>" <?php if ($product->getColor() === $color) { ?>
                                     selected <?php } ?>> <?= $color ?> </option>
                                 <?php
                             }
                             ?>

                         </select>
                         <br>
                         Product material :
                         <select id="select-material" name="product_material">
                             <?php
                             foreach ($materials as $material) {
                                 ?>
                                 <option value="<?= $material ?>" <?php if ($product->getMaterial() === $material) { ?>
                                     selected <?php } ?>> <?= $material ?> </option>
                                 <?php
                             }
                             ?>

                         </select>

                         <br>


                         Product information:
                         <input type="text" name="product_info" maxlength="150"
                                value="<?= $product->getInfo() ?>" > <br>

                         <br>


                         <?php
                         foreach ($sizes as $size) {
                             /* @var $size \model\Size */
                             ?>
                             Size : <?= $size->getSizeNumber() ?>
                             Quantity: <input class="input_inline_block" type="number"
                                              name="<?= $size->getSizeNumber() ?>"
                                              value="<?= $size->getSizeQuantity() ?>">
                             <br>
                             <?php

                         }

                         ?>

                         <input type="hidden" name="product_id" value="<?= $product_id ?>">
                         <input class="button" type="submit" name="change_product" value="Change product">

                     </form>
                 </div>
             </div>
             <?php
         }
     }
    }else {
        ?>
        <h1>Sorry!!!! Looks like you are not an admin! </h1>
        <?php
    }

    ?>
</div>
<script>

</script>