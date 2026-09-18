export type DeviceReading = {
    payload: Record<string, unknown>;
    received_at: string;
};

export type Device = {
    id: number;
    name: string;
    is_active: boolean;
    created_at: string | null;
    latest_message: DeviceReading | null;
};

export type DevicePageProps = {
    devices: Device[];
    deviceToken?: string | null;
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
        };
    };
    errors: Record<string, string>;
};
