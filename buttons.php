<form method="post" action="cart_add.php">
  <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
  <input type="hidden" name="variant_id" value="<?= $variant['id'] ?? '' ?>">
  <input type="number" name="qty" value="1" min="1">
  <button type="submit" class="btn-primary">Add to Cart</button>
</form>
