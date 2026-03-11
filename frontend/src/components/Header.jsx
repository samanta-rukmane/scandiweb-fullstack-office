import { Link } from "react-router-dom";
import CategoryList from "./CategoryList";

export default function Header({ totalItems, openCart }) {
  return (
    <header className="header">
      <div className="container header-inner">

        <CategoryList />

        <div className="logo">
          <Link to="/" style={{ textDecoration: "none", color: "#1D1F22" }}>
            Fullstack eCommerce
          </Link>
        </div>

        <button
          data-testid="cart-btn"
          className="cart-button"
          onClick={openCart}
        >
          🛒
          {totalItems > 0 && (
            <span data-testid="cart-bubble" className="cart-bubble">
              {totalItems}
            </span>
          )}
        </button>

      </div>
    </header>
  );
}