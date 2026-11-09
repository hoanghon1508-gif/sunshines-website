'use client';

import { useId, useRef, useState } from 'react';
import { isValidPhone, normalizePhone } from '@/lib/validation';

type Source = 'contact' | 'consult' | 'promo';

interface Props {
  source: Source;
  product?: string; // slug sản phẩm (form tư vấn)
  showName?: boolean;
  showEmail?: boolean;
  showMessage?: boolean;
  button?: string;
  layout?: 'stacked' | 'inline';
  className?: string;
}

const MESSAGES: Record<string, string> = {
  invalid: 'Vui lòng kiểm tra lại số điện thoại và các thông tin bắt buộc.',
  limit: 'Bạn gửi quá nhiều lần. Vui lòng thử lại sau ít phút.',
  error: 'Chưa gửi được. Vui lòng thử lại hoặc gọi hotline.',
};

/** Form khách hàng (liên hệ, đăng ký tư vấn). Gửi tới /api/lead. */
export function LeadForm({
  source,
  product,
  showName = true,
  showEmail = false,
  showMessage = false,
  button = 'Gửi',
  layout = 'stacked',
  className = '',
}: Props) {
  const uid = useId();
  const startedAt = useRef(Date.now());
  const [state, setState] = useState<'idle' | 'sending' | 'success' | 'error'>('idle');
  const [error, setError] = useState('');
  const isContact = source === 'contact';

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const form = e.currentTarget;
    const data = new FormData(form);
    const phone = normalizePhone(String(data.get('phone') ?? ''));
    const name = String(data.get('name') ?? '').trim();
    if (!isValidPhone(phone) || (isContact && !name)) {
      setState('error');
      setError(MESSAGES.invalid);
      return;
    }
    setState('sending');
    try {
      const res = await fetch('/api/lead', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          source,
          product,
          name,
          phone,
          email: data.get('email') ?? '',
          message: data.get('message') ?? '',
          website: data.get('website') ?? '',
          startedAt: startedAt.current,
        }),
      });
      const json = await res.json().catch(() => ({}));
      if (res.ok && json.ok) {
        form.reset();
        startedAt.current = Date.now();
        setState('success');
      } else {
        setState('error');
        setError(MESSAGES[json.error] ?? MESSAGES.error);
      }
    } catch {
      setState('error');
      setError(MESSAGES.error);
    }
  }

  return (
    <>
      {state === 'success' && (
        <div className="xanh-notice xanh-notice--success" role="status">
          Cảm ơn bạn! Chúng tôi sẽ liên hệ trong thời gian sớm nhất.
        </div>
      )}
      {state === 'error' && (
        <div className="xanh-notice xanh-notice--error" role="alert">
          {error}
        </div>
      )}
      <form className={`xanh-form xanh-form--${layout} ${className}`} onSubmit={onSubmit} noValidate>
        <div className="xanh-form__hp" aria-hidden="true">
          <label htmlFor={`${uid}-web`}>Website</label>
          <input type="text" id={`${uid}-web`} name="website" tabIndex={-1} autoComplete="off" />
        </div>
        <div className="xanh-form__fields">
          {showName && (
            <div className="xanh-field">
              <label htmlFor={`${uid}-name`}>Họ và tên{isContact ? ' *' : ''}</label>
              <input type="text" id={`${uid}-name`} name="name" autoComplete="name" maxLength={120} placeholder="Nguyễn Văn A" required={isContact} />
            </div>
          )}
          <div className="xanh-field">
            <label htmlFor={`${uid}-phone`}>Số điện thoại *</label>
            <input type="tel" id={`${uid}-phone`} name="phone" autoComplete="tel" inputMode="tel" required pattern="[0-9 +.\-]{9,15}" placeholder="Số điện thoại của bạn" />
          </div>
          {showEmail && (
            <div className="xanh-field">
              <label htmlFor={`${uid}-email`}>Email</label>
              <input type="email" id={`${uid}-email`} name="email" autoComplete="email" placeholder="email@example.com" />
            </div>
          )}
          {showMessage && (
            <div className="xanh-field xanh-field--full">
              <label htmlFor={`${uid}-msg`}>Nội dung</label>
              <textarea id={`${uid}-msg`} name="message" rows={5} maxLength={3000} placeholder="Bạn cần hỗ trợ gì?" />
            </div>
          )}
        </div>
        <button type="submit" className="btn btn--primary xanh-form__submit" disabled={state === 'sending'}>
          {state === 'sending' ? 'Đang gửi…' : button}
        </button>
      </form>
    </>
  );
}
