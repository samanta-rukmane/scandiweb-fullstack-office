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

  const handlePlaceOrder = async () => {
    if (!cartItems.length) return;

    const orderItems = cartItems.map(item => ({
      productId: item.product.id,
      quantity: item.quantity,
      selectedAttributes: (item.selectedAttributes || []).map(attr => ({
        name: attr.name,
        value: attr.value
      }))
    }));

    const query = `
      mutation CreateOrder($items: [OrderItemInput!]!) {
        createOrder(items: $items) {
          id
          status
        }
      }
    `;

    try {
      console.log("Order payload:", orderItems);
      const response = await fetch('http://localhost:8000/graphql', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ query, variables: { items: orderItems } })
      });

      const { data } = await response.json();

      clearCart();
      onClose();
    } catch (err) {
      console.error('Error placing order:', err);
    }
  };

  const computedTotal = cartItems.reduce(
    (sum, item) => sum + item.quantity * (item.price ?? item.product?.price ?? item.product?.prices?.[0]?.amount ?? 0),
    0
  );

  return (
    <>
      <div
        className="cart-overlay-backdrop"
        onClick={onClose}
      />

      <div
        className="cart-overlay"
        data-testid="cart-overlay"
      >

        <p className="cart-title">
          <strong> My Bag</strong>, {totalItems} {totalItems === 1 ? 'item' : 'items'}
        </p>

        {cartItems.length === 0 && (
          <p className="cart-empty">
            Your cart is empty
          </p>
        )}

        {cartItems.map((item) => {
          const safeProduct = item.product || {
            id: 'unknown',
            name: 'Unknown Product',
            attributes: [],
            gallery: []
          };

          return (
            <CartItem
              key={
                item.id ||
                `${safeProduct.id}-${(item.selectedAttributes || []).map(a => a.value).join('-')}`
              }
              item={{
                ...item,
                product: safeProduct,
                selectedAttributes: item.selectedAttributes || []
              }}
              changeQuantity={changeQuantity}
            />
          );
        })}

        <div className="cart-total-row">
          <span className="total-label">Total</span>
          <span className="total-value" data-testid="cart-total">
            ${computedTotal.toFixed(2)}
          </span>
        </div>

        <div className="cart-actions">
          <button
            data-testid="place-order"
            disabled={cartItems.length === 0}
            className="place-order-btn"
            onClick={handlePlaceOrder}
          >
            PLACE ORDER
          </button>
        </div>
      </div>
    </>
  );
}