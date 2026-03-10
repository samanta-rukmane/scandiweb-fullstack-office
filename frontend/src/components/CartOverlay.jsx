import CartItem from "./CartItem";

export default function CartOverlay({ cartItems, changeQuantity, total, clearCart, onClose, placeOrder }) {
  return (
    <>
      <div className="cart-overlay-backdrop" onClick={onClose}></div>

      <div className="cart-overlay">
        <h2 className="text-xl font-bold mb-4"> Cart </h2>

        {cartItems.length === 0 && (
          <p className="text-gray-500 text-center"> Your cart is empty</p>
        )}

        {cartItems.map(item => (
          <CartItem key={item.product.id + JSON.stringify(item.product.selectedAttributes)} item={item} changeQuantity={changeQuantity} />
        ))}

        <p data-testid="cart-total" className="font-bold mt-4">
          Total: ${total.toFixed(2)}
        </p>

        <button
          data-testid="place-order"
          disabled={cartItems.length === 0}
          className={`w-full mt-4 py-2 rounded text-white ${
            cartItems.length === 0 ? "bg-gray-400 cursor-not-allowed" : "bg-green-600 hover:bg-green-500"
          }`}
          onClick={() => {
            placeOrder(cartItems);
            clearCart();
            onClose();
          }}
        >
          Place Order
        </button>

        <button
          className="absolute top-2 right-2 text-gray-500 hover:text-gray-800"
          onClick={onClose}
        >
          ✕
        </button>
      </div>
    </>
  );
}