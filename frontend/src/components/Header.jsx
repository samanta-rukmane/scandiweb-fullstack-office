import CategoryList from "./CategoryList";

export default function Header({ totalItems, openCart }) {
  return (
    <header className="header">
      <div className="container header-inner">

        <CategoryList />

        <div className="logo">
          Fullstack eCommerce
        </div>

        <button
          data-testid="cart-btn"
          className="cart-button"
          onClick={openCart}
        >
          🛒

          {totalItems > 0 && (
            <span className="cart-bubble">
              {totalItems}
            </span>
          )}
        </button>

      </div>
    </header>
  );
}