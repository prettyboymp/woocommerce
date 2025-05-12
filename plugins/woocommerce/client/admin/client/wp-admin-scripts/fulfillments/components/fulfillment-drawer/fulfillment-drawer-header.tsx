export default function FulfillmentsDrawerHeader( {
	orderId,
	onClose,
}: {
	orderId: number | null;
	onClose: () => void;
} ) {
	return (
		<div className="drawer-header">
			<div className="drawer-header__title">
				<h2>#{ orderId } Michael Jones</h2>
				<button
					className="drawer-header__close-button"
					onClick={ onClose }
				>
					×
				</button>
			</div>
			<p>February 19, 2020, 6:22pm</p>
		</div>
	);
}
