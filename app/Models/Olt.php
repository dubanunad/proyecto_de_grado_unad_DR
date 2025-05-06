<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpseclib3\Net\SSH2;

class Olt extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'ip_address',
        'ssh_port',
        'telnet_port',
        'snmp_port',
        'read_snmp_comunity',
        'write_snmp_comunity',
        'username',
        'password',
        'brand',
        'model',
        'active',
        'temperature',
        'status',
        'uptime'
    ];

    //Relación con onts
    public function onts()
    {
        return $this->hasMany(Ont::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Los atributos que deben ocultarse para los arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function obtenerDatosRemotos()
    {
        $resultado = [
            'status' => 'Desconectado',
            'temperature' => 'N/A',
            'uptime' => 'N/A',
        ];

        try {
            $ssh = new SSH2($this->ip_address, (int) $this->ssh_port);

            // Usar contraseña descifrada para SSH
            if (!$ssh->login($this->username, $this->getPlainPassword())) {
                return $resultado;
            }

            $resultado['status'] = 'Conectado';

            // Ejecutar comandos específicos para Huawei OLT
            $ssh->setTimeout(2); // Evita quedar esperando eternamente
            $ssh->write("enable\n");
            $ssh->read('#');

            // Comando para uptime
            $ssh->setTimeout(2); // Evita quedar esperando eternamente
            $ssh->write("display sysuptime\n");
            $uptimeRaw = $ssh->read('#');

            // Comando para temperatura (ajustado para Huawei)
            $ssh->setTimeout(2); // Evita quedar esperando eternamente
            $ssh->write("display temperature 0/1\n");
            $temperatureRaw = $ssh->read('#');

            // Procesar uptime
            $resultado['uptime'] = $this->procesarUptime($uptimeRaw);

            // Procesar temperatura
            $resultado['temperature'] = $this->procesarTemperatura($temperatureRaw);

            // Guardar los datos obtenidos en la base de datos
            $this->update([
                'status' => true,
                'temperature' => is_numeric($resultado['temperature']) ? $resultado['temperature'] : null,
                'uptime' => $resultado['uptime']
            ]);

        } catch (\Exception $e) {
            // Marcar como desconectado en la base de datos
            $this->update(['status' => false]);
        }

        return $resultado;
    }


    private function procesarUptime($uptimeRaw)
    {
        if (preg_match('/System up time:\s*(.+)/i', $uptimeRaw, $matches)) {
            return trim($matches[1]);
        }
        return 'N/A';
    }

    private function procesarTemperatura($temperatureRaw)
    {
        if (preg_match('/temperature of the board:\s*(\d+)[C]/i', $temperatureRaw, $matches)) {
            return $matches[1] . '°C';
        }
        return 'N/A';
    }


    public function getPlainPassword()
    {
        // NOTA: Esto asume que estás guardando la contraseña en texto plano
        // Si estás usando bcrypt, esto no funcionará y necesitarás otra estrategia
        return $this->password;
    }

}
