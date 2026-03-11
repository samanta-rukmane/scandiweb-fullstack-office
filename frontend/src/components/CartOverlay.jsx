import CartItem from "./CartItem";

export default function CartOverlay({
  cartItems,
  changeQuantity,
  total,
  clearCart,
  onClose,
  placeOrder
}) {

  const totalItems = cartItems.reduce(
    (sum, item) => sum + item.quantity,
    0
  );

  return (
    <>
      <div
        className="cart-overlay-backdrop"
        onClick={onClose}
      />

      <div className="cart-overlay">

        <p className="cart-title">
          <strong>My Bag</strong>, {totalItems} items
        </p>


        {cartItems.length === 0 && (
          <p className="cart-empty">
            Your cart is empty
          </p>
        )}


        {cartItems.map(item => (
          <CartItem
            key={item.product.id + JSON.stringify(item.product.selectedAttributes)}
            item={item}
            changeQuantity={changeQuantity}
          />
        ))}


        <div className="cart-total-row">

          <span>Total</span>

          <span data-testid="cart-total">
            ${total.toFixed(2)}
          </span>

        </div>


        <div className="cart-actions">

          <button
            className="cart-view-bag-btn"
            onClick={onClose}
          >
            VIEW BAG
          </button>

          <button
            data-testid="place-order"
            disabled={cartItems.length === 0}
            className="cart-checkout-btn"
            onClick={() => {
              placeOrder(cartItems);
              clearCart();
              onClose();
            }}
          >
            CHECK OUT
          </button>

        </div>

      </div>
    </>
  );
}