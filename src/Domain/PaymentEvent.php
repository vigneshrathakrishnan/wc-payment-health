<?php
declare(strict_types=1);

namespace WCPH\Domain;

// $result: success | failure (payment outcome, not order lifecycle)
final class PaymentEvent
{
    public function __construct(
        private int $orderId,
        private string $gateway,
        private string $method,
        private string $result,
        private string $occurredAt,
        private string $message,
        private string $source
    ) {}

    public function toArray(): array
    {
        return [
            'order_id'    => $this->orderId,
            'gateway'     => $this->gateway,
            'method'      => $this->method,
            'result'      => $this->result,
            'occurred_at' => $this->occurredAt,
            'message'     => $this->message,
            'source'      => $this->source,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['order_id'] ?? 0),
            (string) ($data['gateway']     ?? 'unknown'),
            (string) ($data['method']      ?? 'unknown'),
            (string) ($data['result']      ?? 'unknown'),
            (string) ($data['occurred_at'] ?? ''),
            (string) ($data['message']     ?? ''),
            (string) ($data['source']      ?? '')
        );
    }
}
